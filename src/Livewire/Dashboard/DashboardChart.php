<?php

namespace Spatie\Mailcoach\Livewire\Dashboard;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class DashboardChart extends Component
{
    use UsesMailcoachModels;

    // Chart
    public string $start;

    public string $end;

    public Collection $stats;

    protected int $startSubscriptionsCount;

    public function mount(): void
    {
        if (! isset($this->start)) {
            $firstSubscriber = self::getSubscriberClass()::subscribed()->orderBy('subscribed_at', 'asc')->first();

            if (! $firstSubscriber || $firstSubscriber->subscribed_at < now()->subMonths(2)) {
                $this->start = now()->subMonths(2)->format('Y-m-d');
            } elseif ($firstSubscriber->subscribed_at > now()->subWeeks(2)) {
                $this->start = now()->subWeeks(2)->format('Y-m-d');
            } else {
                $this->start = $firstSubscriber->subscribed_at->format('Y-m-d');
            }
        }

        $this->end ??= now()->format('Y-m-d');
    }

    public function updatedStart($newStart): void
    {
        if (Date::parse($newStart) > Date::parse($this->end)) {
            $this->start = $this->end;
        }
    }

    public function updatedEnd($newEnd): void
    {
        if (Date::parse($newEnd) > Date::now()) {
            $this->end = Date::now()->format('Y-m-d');
        }

        if (Date::parse($newEnd) < Date::parse($this->start)) {
            $this->end = $this->start;
        }
    }

    public function placeholder()
    {
        return view('mailcoach::app.partials.dashboard-chart', ['placeholder' => true]);
    }

    public function render()
    {
        $this->startSubscriptionsCount = self::getSubscriberClass()::query()
            ->whereNotNull('subscribed_at')
            ->where('subscribed_at', '<', $this->start)
            ->where(function ($query) {
                $query
                    ->whereNull('unsubscribed_at')
                    ->orWhere('unsubscribed_at', '>=', $this->start);
            })
            ->count();

        $this->stats = $this->createStats();

        return view('mailcoach::app.partials.dashboard-chart');
    }

    protected function createStats(): Collection
    {
        $start = Date::parse($this->start)->startOfDay();
        $end = Date::parse($this->end)->endOfDay();

        $subscribedAtDateFormat = database_date_format_function('subscribed_at', '%Y-%m-%d');
        $subscribes = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getSubscriberTableName())
            ->selectRaw("count(*) as subscribed_count, {$subscribedAtDateFormat} as subscribed_day")
            ->whereBetween('subscribed_at', [$start, $end])
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        if (! $subscribes->count()) {
            return collect();
        }

        $unsubscribedAtDateFormat = database_date_format_function('unsubscribed_at', '%Y-%m-%d');
        $unsubscribes = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getSubscriberTableName())
            ->selectRaw("count(*) as unsubscribe_count, {$unsubscribedAtDateFormat} as unsubscribe_day")
            ->whereBetween('unsubscribed_at', [$start, $end])
            ->orderBy('unsubscribe_day')
            ->groupBy('unsubscribe_day')
            ->get();

        $subscriberTotal = $this->startSubscriptionsCount;
        $period = CarbonPeriod::create($start, $end);

        $subscribers = collect($period->toArray())->map(function ($date) use (&$subscriberTotal, $subscribes, $unsubscribes) {
            $subscribeCount = $subscribes->where('subscribed_day', $date->format('Y-m-d'))->first();
            $subscriberTotal += (optional($subscribeCount)->subscribed_count ?? 0);
            $unsubscribeCount = $unsubscribes->where('unsubscribe_day', $date->format('Y-m-d'))->first();
            $subscriberTotal -= (optional($unsubscribeCount)->unsubscribe_count ?? 0);

            return [
                'label' => $date->format('M d'),
                'subscribers' => $subscriberTotal,
                'subscribes' => optional($subscribeCount)->subscribed_count ?? 0,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count ?? 0,
            ];
        });

        $campaigns = self::getCampaignClass()::query()
            ->whereBetween('sent_at', [$start, $end])
            ->where('status', CampaignStatus::Sent)
            ->select(['id', 'name', 'sent_at'])
            ->get();

        $lastStats = [
            'subscribers' => $this->startSubscriptionsCount,
        ];

        return collect(CarbonPeriod::create($start, '1 day', $end))->map(function (CarbonInterface $day) use (
            $campaigns,
            $subscribers,
            &$lastStats
        ) {
            $day = $day->toImmutable();

            $label = $day->startOfDay()->format('M d');

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            $subscribers = $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'subscribes' => 0,
                'unsubscribes' => 0,
            ];

            $subscribers['campaigns'] = $campaigns
                ->whereBetween('sent_at', [$day->startOfDay(), $day->endOfDay()])
                ->map(fn (Campaign $campaign) => ['id' => $campaign->id, 'name' => $campaign->name, 'sent_at' => $campaign->sent_at->format('M d')])
                ->toArray();

            return $subscribers;
        });
    }
}
