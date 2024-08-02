<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Mailcoach;
use Throwable;

class CompleteSubscriberImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function retryUntil(): CarbonInterface
    {
        return now()->addHours(12);
    }

    public function __construct(
        private SubscriberImport $subscriberImport,
        private int $totalRows,
        private ?User $user,
        private bool $sendNotification = true,
    ) {
    }

    public function handle()
    {
        if ($this->totalRows > $this->subscriberImport->imported_subscribers_count) {
            $this->release(60); // Try again in 1 minute

            return;
        }

        $this->subscriberImport->update([
            'status' => SubscriberImportStatus::Completed,
        ]);
        $this->subscriberImport->saveErrorReport();

        if (! $this->user) {
            return;
        }

        if (! Mailcoach::defaultTransactionalMailer()) {
            return;
        }

        if ($this->sendNotification) {
            try {
                Mail::mailer(Mailcoach::defaultTransactionalMailer())
                    ->to($this->user->email)->send(new ImportSubscribersResultMail($this->subscriberImport));
            } catch (Throwable $e) {
                report($e);

                return;
            }
        }
    }

    public function failed(Throwable $exception)
    {
        $this->subscriberImport->addError($exception->getMessage());
        $this->subscriberImport->update([
            'status' => SubscriberImportStatus::Failed,
        ]);
    }
}
