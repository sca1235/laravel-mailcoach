<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        if (! $campaign->isSending()) {
            return;
        }

        $this
            ->updateSegmentDescription($campaign)
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->handleSplitTest($campaign, $stopExecutingAt)
            ?->dispatchCreateSendJobs(
                campaign: $campaign,
                contentItem: $campaign->isSplitTested()
                    ? $campaign->splitTestWinner
                    : $campaign->contentItem,
                stopExecutingAt: $stopExecutingAt,
            )->markCampaignAsSent($campaign);
    }

    protected function updateSegmentDescription(Campaign $campaign): static
    {
        $campaign->update([
            'segment_description' => $campaign->getSegment()->description(),
        ]);

        return $this;
    }

    protected function prepareEmailHtml(Campaign $campaign): static
    {
        $campaign->contentItems->each(function (ContentItem $contentItem) {
            $prepareEmailHtmlAction = Mailcoach::getSharedActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
            $prepareEmailHtmlAction->execute($contentItem);
        });

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign): static
    {
        $campaign->contentItems->each(function (ContentItem $contentItem) {
            $prepareWebviewHtmlAction = Mailcoach::getSharedActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);
            $prepareWebviewHtmlAction->execute($contentItem);
        });

        return $this;
    }

    protected function handleSplitTest(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): ?static
    {
        if (! $campaign->isSplitTested()) {
            return $this;
        }

        if ($campaign->hasSplitTestWinner()) {
            return $this;
        }

        $subscribersQuery = $this->getSubscribersQuery($campaign);

        // By default, we'll take 30% of the subscribers and divide it by the amount of splits
        $splitSize = $campaign->split_test_split_size_percentage ?? 30;

        // If we are in the first stage of the test, send each content item to X% of the subscribers
        $splitSubscriberCount = max(1, floor($subscribersQuery->count() / 100 * $splitSize / $campaign->contentItems->count()));

        foreach ($campaign->contentItems as $index => $contentItem) {
            $splitSubscribersQuery = $subscribersQuery
                ->clone()
                ->offset($index * $splitSubscriberCount)
                ->limit($splitSubscriberCount);

            // These need to be done with a subquery, otherwise aggregate methods with offset & limit don't work
            $firstId = DB::query()->fromSub($splitSubscribersQuery, 'subscribers')->min('id');
            $lastId = DB::query()->fromSub($splitSubscribersQuery, 'subscribers')->max('id');

            $this->dispatchCreateSendJobs($campaign, $contentItem, $firstId, $lastId, $stopExecutingAt);
        }

        if ($campaign->hasPendingSends()) {
            return null;
        }

        // If all sends have been dispatched & sent, mark the start of the test
        if (! $campaign->isSplitTestStarted()) {
            $campaign->markSplitTestStarted();
        }

        // Make sure the wait time is over
        if (! $campaign->splitWaitTimeIsOver()) {
            return null;
        }

        // Determine a winner
        $determineSplitTestWinnerAction = Mailcoach::getCampaignActionClass('determine_split_test_winner', DetermineSplitTestWinnerAction::class);
        $determineSplitTestWinnerAction->execute($campaign);

        $campaign->splitTestWinner->update([
            'all_sends_created_at' => null,
            'all_sends_dispatched_at' => null,
            'sent_to_number_of_subscribers' => $campaign->splitTestWinner->sent_to_number_of_subscribers + $subscribersQuery->withoutSendsForCampaign($campaign)->count(),
        ]);

        return $this;
    }

    protected function markCampaignAsSent(Campaign $campaign): void
    {
        if ($campaign->hasPendingSends()) {
            dispatch(new SendCampaignMailsJob());

            return;
        }

        $campaign->load('contentItems');

        $allSendsCreatedAt = $campaign->contentItems->max('all_sends_created_at');

        $subscribersQueryCount = $this->getSubscribersQuery($campaign)
            ->when($allSendsCreatedAt, fn (Builder $query) => $query->where('subscribed_at', '<', $allSendsCreatedAt))
            ->count();

        if ($subscribersQueryCount > $campaign->sendsCount()) {
            if (
                $campaign->contentItems->whereNull('all_sends_created_at')->count() > 0
                || $campaign->contentItems->whereNull('all_sends_dispatched_at')->count() > 0
            ) {
                return;
            }

            /**
             * If the campaign has no pending sends anymore, but all
             * content items have all sends created and dispatched.
             * Subscribers have been added in the meantime.
             */
            $campaign->contentItems()->update([
                'all_sends_created_at' => null,
                'all_sends_dispatched_at' => null,
            ]);

            return;
        }

        $campaign->markAsSent();

        event(new CampaignSentEvent($campaign));
    }

    protected function dispatchCreateSendJobs(
        Campaign $campaign,
        ContentItem $contentItem,
        ?int $firstId = null,
        ?int $lastId = null,
        ?CarbonInterface $stopExecutingAt = null,
    ): static {
        if ($contentItem->allSendsCreated()) {
            return $this;
        }

        $subscribersQuery = $this->getSubscribersQuery($campaign);
        if ($firstId) {
            $subscribersQuery->where('id', '>=', $firstId);
        }
        if ($lastId) {
            $subscribersQuery->where('id', '<=', $lastId);
        }

        $subscribersQueryCount = $subscribersQuery->count();

        $contentItem->update(['sent_to_number_of_subscribers' => $subscribersQueryCount]);

        $dispatched = 0;

        $contentItemIds = $campaign->contentItems->pluck('id')->toArray();

        // So the queued job doesn't load these relations
        $campaign->unsetRelations();

        $subscribersQuery
            ->withoutSendsForContentItems($contentItemIds)
            ->select(self::getSubscriberTableName().'.id')
            ->lazyById()
            ->each(function (Subscriber $subscriber) use (&$dispatched, $contentItem, $stopExecutingAt, $campaign) {
                $this->haltWhenApproachingTimeLimit($stopExecutingAt);

                dispatch(new CreateCampaignSendJob($campaign, $contentItem, $subscriber));

                $dispatched++;
            });

        if ($dispatched > 0) {
            return $this;
        }

        $contentItem->markAsAllSendsCreated();

        return $this;
    }

    protected function haltWhenApproachingTimeLimit(?CarbonInterface $stopExecutingAt): void
    {
        if (is_null($stopExecutingAt)) {
            return;
        }

        if ($stopExecutingAt->diffInSeconds(absolute: true) > 10) {
            return;
        }

        throw SendCampaignTimeLimitApproaching::make();
    }

    /** @return Builder<Subscriber> */
    protected function getSubscribersQuery(Campaign $campaign): Builder
    {
        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        return $subscribersQuery->orderBy('id');
    }
}
