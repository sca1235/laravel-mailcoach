<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class CampaignStatisticsComponent extends Component
{
    use UsesMailcoachModels;

    public Campaign $campaign;

    // Chart
    public Collection $stats;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function render(): View
    {
        $this->stats = $this->createStats();

        return view('mailcoach::app.campaigns.partials.chart');
    }

    protected function createStats(): Collection
    {
        if (! $this->campaign->wasAlreadySent()) {
            return collect();
        }

        if (! $this->campaign->openCount()) {
            return collect();
        }

        $start = $this->campaign->sent_at->startOfHour()->toImmutable();

        if ($this->campaign->openCount() > 0) {
            $firstOpenCreatedAt = self::getOpenClass()::query()
                ->whereIn('content_item_id', $this->campaign->contentItems->pluck('id'))
                ->orderBy('created_at')
                ->first()
                ?->created_at;

            if ($firstOpenCreatedAt && $firstOpenCreatedAt < $start) {
                $start = $firstOpenCreatedAt->startOfHour()->toImmutable();
            }
        }

        $end = self::getOpenClass()::query()
            ->whereIn('content_item_id', $this->campaign->contentItems->pluck('id'))
            ->orderByDesc('created_at')
            ->first()
            ?->created_at;
        $limit = $start->copy()->addHours(24 * 2);

        if (is_null($end) || $limit->isBefore($end)) {
            $end = $limit;
        }

        $openTable = self::getOpenTableName();
        $clickTable = self::getClickTableName();
        $linkTable = self::getLinkTableName();

        $createdAtDateFormat = database_date_format_function('created_at', '%Y-%m-%d %H:%i');

        $opensPerMinute = DB::connection(Mailcoach::getDatabaseConnection())
            ->table($openTable)
            ->whereIn('content_item_id', $this->campaign->contentItems->pluck('id'))
            ->selectRaw("{$createdAtDateFormat} as minute, COUNT(*) as opens")
            ->groupBy('minute')
            ->get();

        $clickTableCreatedAtDateFormat = database_date_format_function("{$clickTable}.created_at", '%Y-%m-%d %H:%i');

        $clicksPerMinute = DB::connection(Mailcoach::getDatabaseConnection())
            ->table($clickTable)
            ->join($linkTable, 'link_id', '=', $linkTable.'.id')
            ->whereIn('content_item_id', $this->campaign->contentItems->pluck('id'))
            ->selectRaw("{$clickTableCreatedAtDateFormat} as minute, COUNT(*) as clicks")
            ->groupBy('minute')
            ->get();

        return collect(CarbonPeriod::create($start, '10 minutes', $end))->map(function (CarbonInterface $minutes) use ($opensPerMinute, $clicksPerMinute) {
            $minutes = $minutes->toImmutable();

            return [
                'label' => $minutes->isoFormat('dd HH:mm'),
                'opens' => $opensPerMinute->whereBetween('minute', [$minutes->format('Y-m-d H:i'), $minutes->addMinutes(10)->format('Y-m-d H:i')])->sum('opens'),
                'clicks' => $clicksPerMinute->whereBetween('minute', [$minutes->format('Y-m-d H:i'), $minutes->addMinutes(10)->format('Y-m-d H:i')])->sum('clicks'),
            ];
        });
    }
}
