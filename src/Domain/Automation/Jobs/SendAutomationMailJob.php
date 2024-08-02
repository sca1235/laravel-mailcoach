<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendAutomationMailJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $maxExceptions = 3;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->pendingSend->id}";
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHours(3);
    }

    public function __construct(public Send $pendingSend)
    {
        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Mailcoach::getSharedActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware(): array
    {
        $mailer = $this->pendingSend->subscriber->emailList->automation_mailer ?? Mailcoach::defaultAutomationMailer();

        $rateLimitedMiddleware = (new RateLimited(useRedis: false))
            ->key('mailer-throttle-'.$mailer)
            ->allow(config("mail.mailers.{$mailer}.mails_per_timespan", 10))
            ->everySeconds(config("mail.mailers.{$mailer}.timespan_in_seconds", 1))
            ->releaseAfterOneSecond();

        return [$rateLimitedMiddleware];
    }
}
