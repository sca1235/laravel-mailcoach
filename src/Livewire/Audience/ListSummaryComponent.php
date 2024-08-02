<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;

class ListSummaryComponent extends Component
{
    use UsesMailcoachModels;

    public EmailList $emailList;

    // Filters
    protected $queryString = ['start', 'end'];

    public ?string $start = null;

    public ?string $end = null;

    // Counts
    public int $totalSubscriptionsCount;

    public int $totalUnsubscribeCount;

    public int $startSubscriptionsCount;

    public int $startUnsubscribeCount;

    // Chart
    public ?Collection $stats = null;

    public bool $readyToLoad = false;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        $this->start ??= now()->subDays(29)->format('Y-m-d');
        $this->end ??= now()->format('Y-m-d');

        app(MainNavigation::class)->activeSection()->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList));
    }

    public function updatedStart($newStart)
    {
        if (Date::parse($newStart) > Date::parse($this->end)) {
            $this->start = $this->end;
        }
    }

    public function updatedEnd($newEnd)
    {
        if (Date::parse($newEnd) > Date::now()) {
            $this->end = Date::now()->format('Y-m-d');
        }

        if (Date::parse($newEnd) < Date::parse($this->start)) {
            $this->end = $this->start;
        }
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render(): View
    {
        $data = [];

        if ($this->readyToLoad) {
            $this->totalSubscriptionsCount = $this->emailList->totalSubscriptionsCount();
            $this->totalUnsubscribeCount = $this->emailList->unsubscribedCount();

            $this->startSubscriptionsCount = $this->emailList->subscribers()
                ->where('subscribed_at', '<', $this->start)
                ->count();

            $this->startUnsubscribeCount = $this->emailList->allSubscribers()
                ->unsubscribed()
                ->where('unsubscribed_at', '>', $this->start)
                ->count();

            $this->stats = $this->createStats();

            $data = [
                'totalSubscriptionsCount' => $this->totalSubscriptionsCount(),
                'growthRate' => $this->growthRate(),
                'churnRate' => $this->churnRate(),
                'averageOpenRate' => $this->averageOpenRate(),
                'averageClickRate' => $this->averageClickRate(),
                'averageUnsubscribeRate' => $this->averageUnsubscribeRate(),
                'averageBounceRate' => $this->averageBounceRate(),
            ];
        }

        return view('mailcoach::app.emailLists.summary', $data)
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __mc('Performance'),
                'emailList' => $this->emailList,
                'hideCard' => true,
            ]);
    }

    protected function createStats(): Collection
    {
        $start = Date::parse($this->start);
        $end = Date::parse($this->end);

        $diff = $start->diffInSeconds($end, absolute: true);
        $interval = match (true) {
            $diff > 60 * 60 * 24 * 2 => 'day', // > 7 days
            default => 'hour',
        };

        $start = $start->startOf($interval === 'hour' ? 'day' : $interval);
        $end = $end->endOf($interval === 'hour' ? 'day' : $interval);

        $subscribedAtDateFormat = match ($interval) {
            'hour' => database_date_format_function('subscribed_at', '%Y-%m-%d %H:%I'),
            'day' => database_date_format_function('subscribed_at', '%Y-%m-%d'),
        };

        $subscribes = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getSubscriberTableName())
            ->selectRaw("count(*) as subscribed_count, {$subscribedAtDateFormat} as subscribed_day")
            ->where('email_list_id', $this->emailList->id)
            ->whereBetween('subscribed_at', [$start, $end])
            ->whereNull('unsubscribed_at')
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        $unsubscribedAtDateFormat = match ($interval) {
            'hour' => database_date_format_function('unsubscribed_at', '%Y-%m-%d %H:%I'),
            'day' => database_date_format_function('unsubscribed_at', '%Y-%m-%d'),
        };

        $unsubscribes = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getSubscriberTableName())
            ->selectRaw("count(*) as unsubscribe_count, {$unsubscribedAtDateFormat} as unsubscribe_day")
            ->where('email_list_id', $this->emailList->id)
            ->whereBetween('unsubscribed_at', [$start, $end])
            ->whereNotNull('unsubscribed_at')
            ->orderBy('unsubscribe_day')
            ->groupBy('unsubscribe_day')
            ->get();

        $subscriberTotal = $this->startSubscriptionsCount;

        $subscribers = collect(CarbonPeriod::create($start, '1 '.$interval, $end))->map(function (CarbonInterface $day) use ($interval, &$subscriberTotal, $subscribes, $unsubscribes) {
            $format = match ($interval) {
                'hour' => 'Y-m-d H:i:s',
                'day' => 'Y-m-d',
            };

            $subscribeResult = $subscribes->where('subscribed_day', $day->format($format))->first();
            $unsubscribeResult = $unsubscribes->where('unsubscribe_day', $day->format($format))->first();

            $subscriberTotal += $subscribeResult?->subscribed_count ?? 0;
            $subscriberTotal -= $unsubscribeResult?->subscribed_count ?? 0;

            return [
                'label' => match ($interval) {
                    'hour' => $day->startOf($interval)->format('y M d H:i'),
                    'day' => $day->startOf($interval)->format('y M d'),
                },
                'subscribers' => $subscriberTotal,
                'subscribes' => $subscribeResult?->subscribed_count ?? 0,
                'unsubscribes' => $unsubscribeResult?->unsubscribe_count ?? 0,
            ];
        });

        $lastStats = [
            'subscribers' => $this->startSubscriptionsCount,
        ];

        return collect(CarbonPeriod::create($start, '1 '.$interval, $end))->map(function (CarbonInterface $day) use ($interval, $subscribers, &$lastStats) {
            $label = match ($interval) {
                'hour' => $day->startOf($interval)->format('y M d H:i'),
                'day' => $day->startOf($interval)->format('y M d'),
            };

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            return $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'subscribes' => 0,
                'unsubscribes' => 0,
            ];
        });
    }

    public function averageOpenRate(): float
    {
        return DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getContentItemTableName())
            ->where('model_type', (new (self::getCampaignClass()))->getMorphClass())
            ->whereIn('model_id', $this->emailList->campaigns()->select('id'))
            ->average('open_rate') / 100;
    }

    public function averageClickRate(): float
    {
        return DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getContentItemTableName())
            ->where('model_type', (new (self::getCampaignClass()))->getMorphClass())
            ->whereIn('model_id', $this->emailList->campaigns()->select('id'))
            ->average('click_rate') / 100;
    }

    public function averageUnsubscribeRate(): float
    {
        return DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getContentItemTableName())
            ->where('model_type', (new (self::getCampaignClass()))->getMorphClass())
            ->whereIn('model_id', $this->emailList->campaigns()->select('id'))
            ->average('unsubscribe_rate') / 100;
    }

    public function averageBounceRate(): float
    {
        return DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getContentItemTableName())
            ->where('model_type', (new (self::getCampaignClass()))->getMorphClass())
            ->whereIn('model_id', $this->emailList->campaigns()->select('id'))
            ->average('bounce_rate') / 100;
    }

    public function growthRate(): float
    {
        if ($this->startSubscriptionsCount === 0 && $this->totalSubscriptionsCount === 0) {
            return 0;
        }

        $start = $this->startSubscriptionsCount > 0
            ? $this->startSubscriptionsCount
            : 1;

        // Percent Change = 100 × (Present or Future Value – Past or Present Value) / Past or Present Value
        return round(100 * ($this->totalSubscriptionsCount - $start) / $start, 2);
    }

    public function churnRate(): float
    {
        if ($this->totalSubscriptionsCount === 0) {
            return 0;
        }

        return round($this->startUnsubscribeCount / $this->totalSubscriptionsCount * 100, 2);
    }

    public function totalSubscriptionsCount(): int
    {
        return $this->totalSubscriptionsCount;
    }

    public function startSubscriptionsCount(): int
    {
        return $this->startSubscriptionsCount;
    }

    public function totalUnsubscribeCount(): int
    {
        return $this->totalUnsubscribeCount;
    }

    public function startUnsubscribeCount(): int
    {
        return $this->startUnsubscribeCount;
    }
}
