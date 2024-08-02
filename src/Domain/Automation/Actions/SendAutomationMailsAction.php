<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use ArtisanSdk\RateLimiter\Buckets\Leaky;
use ArtisanSdk\RateLimiter\Limiter;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Exceptions\SendAutomationMailsTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendAutomationMailsAction
{
    use UsesMailcoachModels;

    public function execute(?CarbonInterface $stopExecutingAt = null): void
    {
        $this->retryDispatchForStuckSends();

        self::getSendClass()::query()
            ->undispatched()
            ->whereHas('contentItem', function (Builder $query) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $automationMail */
                $automationMail = new (self::getAutomationMailClass());
                $query->where('model_type', $automationMail->getMorphClass());
            })
            ->lazyById()
            ->each(function (Send $send) use ($stopExecutingAt) {
                $mailer = $send->getMailerKey();
                $mailsPerTimespan = config("mail.mailers.{$mailer}.mails_per_timespan", 10);
                $timespanInSeconds = config("mail.mailers.{$mailer}.timespan_in_seconds", 1);

                $limiter = new Limiter(Cache::store(), new Leaky(
                    key: "dispatch-throttle-{$mailer}",
                    max: $mailsPerTimespan,
                    rate: $mailsPerTimespan / $timespanInSeconds,
                ));

                // should horizon be used, and it is paused, stop dispatching jobs
                if (! app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
                    if ($limiter->exceeded()) {
                        sleep($limiter->backoff());

                        return;
                    }

                    $limiter->hit();

                    dispatch(new SendAutomationMailJob($send));

                    $send->markAsSendingJobDispatched();
                }

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
            });
    }

    /**
     * Dispatch pending sends again that have
     * not been processed in the 30 minutes
     */
    protected function retryDispatchForStuckSends(): void
    {
        $retryQuery = self::getSendClass()::query()
            ->whereHas('contentItem', function (Builder $query) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $automationMail */
                $automationMail = new (self::getAutomationMailClass());
                $query->where('model_type', $automationMail->getMorphClass());
            })
            ->pending()
            ->where('sending_job_dispatched_at', '<', now()->subMinutes(30));

        if ($retryQuery->count() === 0) {
            return;
        }

        $retryQuery->each(function (Send $send) {
            dispatch(new SendAutomationMailJob($send));

            $send->markAsSendingJobDispatched();
        });
    }

    protected function haltWhenApproachingTimeLimit(?CarbonInterface $stopExecutingAt): void
    {
        if (is_null($stopExecutingAt)) {
            return;
        }

        if ($stopExecutingAt->diffInSeconds(absolute: true) > 30) {
            return;
        }

        throw SendAutomationMailsTimeLimitApproaching::make();
    }
}
