<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
<x-mailcoach::card class="px-0">
    <div class="grid grid-cols-3 divide-x divide-snow">
        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $campaign)"
            :stat="number_format($campaign->sentToNumberOfSubscribers())"
            :label="__mc('Recipients')"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.unsubscribes', $campaign)"
            :stat="$campaign->unsubscribeRate() / 100"
            :label="__mc('Unsubscribe Rate')"
            suffix="%"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type][value]=bounced&tableFilters[type][value]=bounced'"
            :stat="$campaign->bounceRate() / 100"
            :label="__mc('Bounce Rate')"
            suffix="%"
        />
    </div>
</x-mailcoach::card>
@if ($campaign->openCount())
    <h2 class="text-xl font-medium">{{ __mc('Opens') }}</h2>
    <x-mailcoach::card class="px-0">
        <div class="grid grid-cols-3 divide-x divide-snow">
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $campaign)"
                :stat="number_format($campaign->openCount())"
                :label="__mc('Opens')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $campaign)"
                :stat="number_format($campaign->uniqueOpenCount())"
                :label="__mc('Unique opens')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $campaign)"
                :stat="$campaign->openRate() / 100"
                :label="__mc('Open rate')"
                suffix="%"
            />
        </div>
    </x-mailcoach::card>
@endif

@if ($campaign->clickCount())
    <h2 class="text-xl font-medium">{{ __mc('Clicks') }}</h2>
    <x-mailcoach::card class="px-0">
        <div class="grid grid-cols-4 divide-x divide-snow">
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $campaign)"
                :stat="number_format($campaign->clickCount())"
                :label="__mc('Clicks')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $campaign)"
                :stat="number_format($campaign->uniqueClickCount())"
                :label="__mc('Unique clicks')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $campaign)"
                :stat="$campaign->clickRate() / 100"
                :label="__mc('Click rate')"
                suffix="%"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $campaign)"
                :stat="number_format($campaign->uniqueClickCount() / $campaign->uniqueOpenCount() * 100)"
                :label="__mc('Click through rate')"
                suffix="%"
            />
        </div>
    </x-mailcoach::card>
@endif

@if ($campaign->isSplitTested())
    <h2 class="text-xl font-medium">{{ __mc('Split tests') }}</h2>
    <div class="grid lg:grid-cols-2 gap-6">
        @foreach ($campaign->contentItems as $index => $contentItem)
            @php($stats = $contentItem->getStatsBefore($campaign->splitTestWinnerDecidedAt()))
            <x-mailcoach::card class="relative overflow-hidden pt-16">
                <div class="absolute flex justify-center w-full top-0 left-0 right-0 pt-4">
                    <div class="mx-auto w-8 h-8 rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold bg-sky-extra-light">
                        {{ $index + 1 }}
                    </div>
                </div>
                @if ($campaign->splitTestWinner?->id === $contentItem->id)
                    <div class="absolute right-0 top-0 -mt-4 -mr-4 h-16 w-16">
                        <div
                            class="absolute transform rotate-45 bg-green text-center font-semibold uppercase tracking-wider text-xs text-white py-1.5 right-[-35px] top-[32px] w-[170px]">
                            {{ __mc('Winner') }}
                        </div>
                    </div>
                @endif
                <h3 class="markup-h3 mb-0 text-center">
                    {{ $contentItem->subject }}
                </h3>

                <div class="grid grid-cols-3 divide-x divide-snow -mx-6">
                    <x-mailcoach::statistic
                        :href="route('mailcoach.campaigns.outbox', $campaign)"
                        :stat="number_format($stats['sent_to_number_of_subscribers'])"
                        :label="__mc('Recipients')"
                    />

                    <x-mailcoach::statistic
                        :href="route('mailcoach.campaigns.unsubscribes', $campaign)"
                        :stat="$stats['unsubscribe_rate'] / 100"
                        :label="__mc('Unsub Rate')"
                        suffix="%"
                    />

                    <x-mailcoach::statistic
                        :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type][value]=bounced&tableFilters[type][value]=bounced'"
                        :stat="$stats['bounce_rate'] / 100"
                        :label="__mc('Bounce Rate')"
                        suffix="%"
                    />
                </div>

                @if($stats['open_count'])
                    <div class="grid grid-cols-3 divide-x divide-snow -mx-6 border-t border-snow pt-6">
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.opens', $campaign)"
                            :stat="number_format($stats['open_count'])"
                            :label="__mc('Opens')"
                        />
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.opens', $campaign)"
                            :stat="number_format($stats['unique_open_count'])"
                            :label="__mc('Unique opens')"
                        />
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.opens', $campaign)"
                            :stat="$stats['open_rate'] / 100"
                            :label="__mc('Open Rate')"
                            suffix="%"
                        />
                    </div>
                @endif

                @if($stats['click_count'])
                    <div class="grid grid-cols-3 divide-x divide-snow -mx-6 border-t border-snow pt-6">
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.clicks', $campaign)"
                            :stat="number_format($stats['click_count'])"
                            :label="__mc('Clicks')"
                        />
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.clicks', $campaign)"
                            :stat="number_format($stats['unique_click_count'])"
                            :label="__mc('Unique clicks')"
                        />
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.clicks', $campaign)"
                            :stat="$stats['click_rate'] / 100"
                            :label="__mc('Click Rate')"
                            suffix="%"
                        />
                    </div>
                @endif
            </x-mailcoach::card>
        @endforeach
    </div>
    @if ($campaign->isSplitTested() && ! $campaign->hasSplitTestWinner())
        @if($campaign->isSplitTestStarted())
            <x-mailcoach::alert type="help" full >
                {!! __mc('Winner will be decided at <strong>:date</strong>.', [
                    'date' => $campaign->split_test_started_at->addMinutes($campaign->split_test_wait_time_in_minutes)->toMailcoachFormat(),
                ]) !!}
            </x-mailcoach::alert>
        @else
            <x-mailcoach::alert type="help" full >
                {{ __mc('Winner will be decided when both splits have finished sending.') }}
            </x-mailcoach::alert>
        @endif
    @endif
@endif
