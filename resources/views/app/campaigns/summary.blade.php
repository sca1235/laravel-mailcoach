<?php
/** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
?>
<div class="card-grid" id="campaign-summary" wire:poll.5s.keep-alive>
    @if ($campaign->isPreparing())
        @include('mailcoach::app.campaigns.partials.campaignStatus', [
            'type' => 'help',
            'status' => __mc('is preparing to send to'),
            'sync' => true,
            'cancelable' => true,
            'progress' => 0,
        ])
    @endif

    @if ($campaign->isCancelled())
        @include('mailcoach::app.campaigns.partials.campaignStatus', [
            'type' => 'error',
            'status' => __mc('sending is cancelled.') . ' ' . __mc('It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                'sendsCount' => number_format($campaign->sendsCount()),
                'sentToNumberOfSubscribers' => number_format($campaign->sentToNumberOfSubscribers()),
                'subscriber' => __mc_choice('subscriber|subscribers', $campaign->sentToNumberOfSubscribers())
            ]),
            'progress' => $campaign->sentToNumberOfSubscribers()
                ? $campaign->sendsCount() / $campaign->sentToNumberOfSubscribers() * 100
                : null,
            'progressClass' => 'bg-red-700'
        ])
        @endif

    @if(($campaign->isSending() && $campaign->sentToNumberOfSubscribers()))
        @if ($campaign->isSplitTested() && !$campaign->hasSplitTestWinner() && $campaign->sendsCount() === $campaign->sentToNumberOfSubscribers())
            @php($status = __mc('is waiting to choose a winning split test. Sending to '))
        @else
            @php($status = $campaign->sendsCount() === $campaign->sentToNumberOfSubscribers()
                ? __mc('is finishing up sending to')
                : __mc('is sending to :sentToNumberOfSubscribers :subscriber of', [
                'sentToNumberOfSubscribers' => number_format($campaign->sentToNumberOfSubscribers()),
                'subscriber' => __mc_choice('subscriber|subscribers', $campaign->sentToNumberOfSubscribers())
            ]))
        @endif

        @include('mailcoach::app.campaigns.partials.campaignStatus', [
            'status' => $status,
            'sync' => true,
            'cancelable' => true,
            'progress' => $campaign->sentToNumberOfSubscribers()
                ? $campaign->sendsCount() / $campaign->sentToNumberOfSubscribers() * 100
                : null,
        ])
    @endif

    @if($campaign->isSent())
        @if($pendingCount = $campaign->contentItems->sum(fn ($contentItem) => $contentItem->sends()->pending()->count()))
            @include('mailcoach::app.campaigns.partials.campaignStatus', [
                'status' => __mc('is retrying <strong>:sendsCount :sends</strong> to', [
                    'sendsCount' => number_format($pendingCount),
                    'sends' => __mc_choice('send|sends', $pendingCount)
                ]),
                'sync' => true,
                'progress' => $campaign->sendsCount()
                    ? (($campaign->sendsCount() - $pendingCount) / $campaign->sendsCount()) * 100
                    : 0,
            ])
        @endif

        @php($count = $campaign->sentToNumberOfSubscribers() - $campaign->contentItems->sum(fn ($contentItem) => $contentItem->sends()->whereNotNull('invalidated_at')->count()))
        @include('mailcoach::app.campaigns.partials.campaignStatus', [
            'type' => 'success',
            'status' => __mc_choice('was delivered successfully to :count subscriber of|was delivered successfully to :count subscribers of', $count, [
                'count' => number_format($count),
            ]),
        ])

        @if($failedSendsCount)
            <x-mailcoach::alert type="error" full>
                {{ __mc('Delivery failed for') }} <strong>{{ number_format($failedSendsCount) }}</strong> {{ __mc_choice('subscriber|subscribers', $failedSendsCount) }}.
                <a class="underline" href="{{ route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=failed' }}">{{ __mc('Check the outbox') }}</a>.
            </x-mailcoach::alert>
        @endif
    @endif

    @if ($campaign->openCount() || $campaign->clickCount())
        <x-mailcoach::card>
            <livewire:mailcoach::campaign-statistics :campaign="$campaign" />
        </x-mailcoach::card>
    @endif

    @include('mailcoach::app.campaigns.partials.statistics')
</div>
