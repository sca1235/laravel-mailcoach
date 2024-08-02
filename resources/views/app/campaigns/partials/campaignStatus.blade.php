<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>

@props([
    'campaign',
    'status',
    'type' => 'help',
    'sync' => false,
    'cancelable' => false,
    'progress' => null,
    'progressClass' => '',
])

<x-dynamic-component :component="'mailcoach::alert'" :type="$type" :sync="$sync" :full="true">
    <div class="flex justify-between items-center w-full">
        <div>
            {{ __mc('Campaign') }}
            @if ($campaign->isSent() && !$campaign->disable_webview)
                <span class="font-medium"><a class="underline" target="_blank" href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a></span>
            @else
                <span class="font-medium">{{ $campaign->name }}</span>
            @endif
            {!! $status !!}

            @if($campaign->emailList)
                <a class="underline font-medium" href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
            @else
                &lt;{{ __mc('deleted list') }}&gt;
            @endif

            @if($campaign->usesSegment())
                ({{ $campaign->segment_description }})
            @endif
        </div>

        @if ($cancelable)
            <x-mailcoach::confirm-button
                class="ml-auto text-red hover:text-red-dark underline"
                onConfirm="() => $wire.cancelSending()"
                :confirm-text="__mc('Are you sure you want to cancel sending this campaign?')">
                {{ __mc('Cancel') }}
            </x-mailcoach::confirm-button>
        @endif

        @if($campaign->isCancelled())
            <x-mailcoach::confirm-button
                class="flex-initial ml-auto underline"
                onConfirm="() => $wire.resend()"
                :confirm-text="__mc('Are you sure you want to continue sending this campaign?')">
                Continue
            </x-mailcoach::confirm-button>
        @endif
    </div>
    @if (! is_null($progress))
        <div class="h-2 rounded-full overflow-hidden border border-navy mt-6">
            <div class="rounded-full h-full bg-navy transition-all duration-500 ease {{ $progressClass }}" style="width: {{ $progress }}%"></div>
        </div>
    @endif
</x-dynamic-component>
