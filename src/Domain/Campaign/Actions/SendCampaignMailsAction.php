<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use ArtisanSdk\RateLimiter\Buckets\Leaky;
use ArtisanSdk\RateLimiter\Limiter;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendCampaignMailsAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        foreach ($campaign->contentItems as $contentItem) {
            $this->retryDispatchForStuckSends($contentItem, $stopExecutingAt);

            if (! $contentItem->sends()->undispatched()->count()) {
                if ($contentItem->allSendsCreated() && ! $contentItem->allMailSendingJobsDispatched()) {
                    $contentItem->markAsAllMailSendingJobsDispatched();
                }

                continue;
            }

            $this->dispatchMailSendingJobs($contentItem, $stopExecutingAt);
        }
    }

    /**
     * Dispatch pending sends again that have
     * not been processed in a realistic time
     */
    protected function retryDispatchForStuckSends(ContentItem $contentItem, ?CarbonInterface $stopExecutingAt = null): void
    {
        $realisticTimeInMinutes = min(
            60 * 3, // SendCampaignMailJob only has 3 hours retryUntil()
            $contentItem->sendTimeInMinutes(),
        );

        $retryQuery = $contentItem
            ->sends()
            ->pending()
            ->where('sending_job_dispatched_at', '<', now()->subMinutes($realisticTimeInMinutes + 15));

        if ($retryQuery->count() === 0) {
            return;
        }

        $contentItem->update(['all_sends_dispatched_at' => null]);

        $uniqueFor = ($contentItem->sendTimeInMinutes() + 15) * 60;

        $retryQuery
            ->select(self::getSendTableName().'.id')
            ->each(function (Send $send) use ($contentItem, $uniqueFor, $stopExecutingAt) {
                $this->dispatchJobForSend($send, $contentItem->getMailerKey(), $uniqueFor);

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
            }, config("mail.mailers.{$contentItem->getMailerKey()}.mails_per_timespan", 10));
    }

    protected function dispatchMailSendingJobs(ContentItem $contentItem, ?CarbonInterface $stopExecutingAt = null): void
    {
        $undispatchedCount = $contentItem->sends()->undispatched()->count();

        $uniqueFor = ($contentItem->sendTimeInMinutes() + 15) * 60;

        while ($undispatchedCount > 0) {
            $contentItem
                ->sends()
                ->undispatched()
                ->select(self::getSendTableName().'.id')
                ->each(function (Send $send) use ($contentItem, $uniqueFor, $stopExecutingAt) {
                    // should horizon be used, and it is paused, stop dispatching jobs
                    $this->dispatchJobForSend($send, $contentItem->getMailerKey(), $uniqueFor);

                    $this->haltWhenApproachingTimeLimit($stopExecutingAt);
                }, config("mail.mailers.{$contentItem->getMailerKey()}.mails_per_timespan", 10));

            $undispatchedCount = $contentItem->sends()->undispatched()->count();
        }

        if (! $contentItem->allSendsCreated()) {
            return;
        }

        $contentItem->markAsAllMailSendingJobsDispatched();
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

    protected function dispatchJobForSend(
        Send $send,
        string $mailer,
        int $uniqueFor
    ): void {
        if (app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
            return;
        }

        $mailsPerTimespan = config("mail.mailers.{$mailer}.mails_per_timespan", 10);
        $timespanInSeconds = config("mail.mailers.{$mailer}.timespan_in_seconds", 1);

        $limiter = new Limiter(Cache::store(), new Leaky(
            key: "dispatch-throttle-{$mailer}",
            max: $mailsPerTimespan,
            rate: $mailsPerTimespan / $timespanInSeconds,
        ));

        if ($limiter->exceeded()) {
            sleep($limiter->backoff());

            return;
        }

        $limiter->hit();

        dispatch(new SendCampaignMailJob($send, $uniqueFor));

        $send->markAsSendingJobDispatched();
    }
}
