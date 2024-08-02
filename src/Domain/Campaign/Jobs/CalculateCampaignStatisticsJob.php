<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class CalculateCampaignStatisticsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    private CarbonInterface $now;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.perform_on_queue.schedule'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        $this->calculateStatisticsOfRecentCampaigns();
    }

    protected function calculateStatisticsOfRecentCampaigns(): void
    {
        $this->now = now();

        collect([
            [CarbonInterval::minute(0), CarbonInterval::minute(5), CarbonInterval::minute(0)],
            [CarbonInterval::minute(5), CarbonInterval::hour(2), CarbonInterval::minute(10)],
            [CarbonInterval::hour(2), CarbonInterval::day(), CarbonInterval::hour()],
            [CarbonInterval::day(), CarbonInterval::weeks(2), CarbonInterval::hour(4)],
        ])->eachSpread(function (CarbonInterval $startInterval, CarbonInterval $endInterval, CarbonInterval $recalculateThreshold) {
            $this
                ->findCampaignsWithStatisticsToRecalculate($startInterval, $endInterval, $recalculateThreshold)
                ->each(function (Campaign $campaign) {
                    if (! $campaign->isSendingOrSent() && ! $campaign->isCancelled()) {
                        return;
                    }

                    $campaign->contentItems->each->dispatchCalculateStatistics();
                });
        });
    }

    public function findCampaignsWithStatisticsToRecalculate(
        CarbonInterval $startInterval,
        CarbonInterval $endInterval,
        CarbonInterval $recalculateThreshold
    ): Collection {
        $periodEnd = $this->now->copy()->subtract($startInterval);
        $periodStart = $this->now->copy()->subtract($endInterval);

        return self::getCampaignClass()::query()
            ->where(function ($query) use ($periodEnd, $periodStart) {
                $query->sentBetween($periodStart, $periodEnd);
            })
            ->where('sent_at', '>', $this->now->copy()->subDays(14))
            ->orWhere('status', CampaignStatus::Sending)
            ->get()
            ->flatMap(fn (Campaign $campaign) => $campaign->contentItems)
            ->filter(function (ContentItem $contentItem) use ($recalculateThreshold) {
                if (is_null($contentItem->statistics_calculated_at)) {
                    return true;
                }

                $threshold = $this->now->copy()->subtract($recalculateThreshold);

                return $contentItem->statistics_calculated_at->isBefore($threshold);
            })
            ->map(fn (ContentItem $contentItem) => $contentItem->model)
            ->unique('id');
    }
}
