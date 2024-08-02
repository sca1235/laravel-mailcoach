<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\EmailListSummaryMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendEmailListSummaryMailJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.perform_on_queue.schedule'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        self::getEmailListClass()::query()
            ->where('report_email_list_summary', true)
            ->each(
                function (EmailList $emailList) {
                    if ($emailList->email_list_summary_sent_at && (int) $emailList->email_list_summary_sent_at->diffInDays(absolute: true) === 0) {
                        return;
                    }

                    $emailListSummaryMail = new EmailListSummaryMail(
                        $emailList,
                        $emailList->email_list_summary_sent_at ?? $emailList->created_at
                    );

                    if (empty($emailList->campaignReportRecipients())) {
                        $emailList->update(['email_list_summary_sent_at' => now()]);

                        return;
                    }

                    Mail::mailer(Mailcoach::defaultTransactionalMailer())
                        ->to($emailList->campaignReportRecipients())
                        ->queue($emailListSummaryMail);

                    $emailList->update(['email_list_summary_sent_at' => now()]);
                }
            );
    }
}
