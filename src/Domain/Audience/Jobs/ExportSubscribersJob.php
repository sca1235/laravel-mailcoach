<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ExportSubscribersAction;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;
use Spatie\Mailcoach\Mailcoach;

class ExportSubscribersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $timeout = 60 * 60; // 1 hour

    public function __construct(
        public SubscriberExport $subscriberExport,
        public ?User $user = null,
        public bool $sendNotification = true
    ) {
        $this->queue = config('mailcoach.audience.perform_on_queue.export_subscribers_job');
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ExportSubscribersAction $exportSubscribersAction */
        $exportSubscribersAction = Mailcoach::getAudienceActionClass('export_subscribers', ExportSubscribersAction::class);
        $exportSubscribersAction->execute(
            subscriberExport: $this->subscriberExport,
            user: $this->user,
            sendNotification: $this->sendNotification
        );
    }
}
