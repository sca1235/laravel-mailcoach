<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RetryPendingSendsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:retry-pending-sends';

    public $description = 'Dispatch a job for each MailSend that has not been sent yet';

    public function handle()
    {
        $pendingSendCount = self::getSendClass()::whereNull('sent_at')->count();

        $this->comment("Dispatching jobs for {$pendingSendCount} pending Sends");

        $campaignClass = self::getCampaignClass();
        $automationMailClass = self::getAutomationMailClass();

        self::getSendClass()::query()
            ->whereNull('sent_at')
            ->whereHas('contentItem', function (Builder $query) use ($campaignClass) {
                $query->where('model_type', (new $campaignClass)->getMorphClass());
            })
            ->each(function (Send $send) {
                dispatch(new SendCampaignMailJob(
                    pendingSend: $send,
                    uniqueFor: ($send->contentItem->sendTimeInMinutes() + 15) * 60),
                );
            });

        self::getSendClass()::query()
            ->whereNull('sent_at')
            ->whereHas('contentItem', function (Builder $query) use ($automationMailClass) {
                $query->where('model_type', (new $automationMailClass)->getMorphClass());
            })
            ->each(function (Send $send) {
                dispatch(new SendAutomationMailJob($send));
            });

        $this->comment('All done!');
    }
}
