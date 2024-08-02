<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class CalculateAutomationMailStatisticsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    /** @todo is this used? */
    private CarbonInterface $now;

    public function __construct(protected ?int $automationMailId = null)
    {
        $this->onQueue(config('mailcoach.perform_on_queue.schedule'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        Cache::put('mailcoach-last-schedule-run', now());

        $this->automationMailId
            ? CalculateStatisticsJob::dispatchSync(self::getAutomationMailClass()::find($this->automationMailId)->contentItem)
            : $this->calculateStatisticsOfAutomationMails();
    }

    protected function calculateStatisticsOfAutomationMails(): void
    {
        $this->now = now();

        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::Started)
            ->with(['allActions'])
            ->get()
            ->flatMap(function (Automation $automation) {
                return $automation->allActions;
            })->filter(function (Action $action) {
                try {
                    return $action->action::class === SendAutomationMailAction::class;
                } catch (ModelNotFoundException) {
                    return false;
                }
            })->map(function (Action $action) {
                return $action->action->automationMail;
            })->each(function (AutomationMail $automationMail) {
                if (! $automationMail instanceof (self::getAutomationMailClass())) {
                    $automationMail = self::getAutomationMailClass()::find($automationMail->getKey());
                }

                $automationMail?->contentItem->dispatchCalculateStatistics();
            });
    }
}
