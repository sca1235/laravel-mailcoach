<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Mailcoach;

class RetrySendingFailedSendsJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var string */
    public $queue;

    public function __construct(public Campaign $campaign)
    {
        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction $retrySendingFailedSendsAction */
        $retrySendingFailedSendsAction = Mailcoach::getCampaignActionClass('retry_sending_failed_sends', RetrySendingFailedSendsAction::class);

        $retrySendingFailedSendsAction->execute($this->campaign);
    }
}
