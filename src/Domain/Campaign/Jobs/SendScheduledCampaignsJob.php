<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendScheduledCampaignsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 90;

    public int $maxExceptions = 5;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.campaigns.perform_on_queue.send_campaign_job'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    public function handle(): void
    {
        $this->sendScheduledCampaigns();
        $this->sendSendingCampaigns();
    }

    protected function sendScheduledCampaigns(): void
    {
        self::getCampaignClass()::shouldBeSentNow()
            ->each(function (Campaign $campaign) {
                $campaign->update(['scheduled_at' => null]);
                $campaign->send();
            });
    }

    protected function sendSendingCampaigns(): void
    {
        $sendCampaignAction = Mailcoach::getCampaignActionClass('send_campaign', SendCampaignAction::class);

        $maxRuntimeInSeconds = max(60, config('mailcoach.campaigns.send_campaign_maximum_job_runtime_in_seconds'));

        self::getCampaignClass()::sending()
            ->each(function (Campaign $campaign) use ($sendCampaignAction, $maxRuntimeInSeconds) {
                $stopExecutingAt = now()->addSeconds($maxRuntimeInSeconds);

                try {
                    $sendCampaignAction->execute($campaign, $stopExecutingAt);
                } catch (SendCampaignTimeLimitApproaching) {
                    return;
                }
            });
    }
}
