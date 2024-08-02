@php($campaign = $getRecord())
<div class="fi-ta-text-item gap-1.5 px-3">
    <span class="link">{{ $campaign->name }}</span>
    @if ($campaign->sends_with_errors_count)
        <div class="flex items-center text-orange gap-1 text-xs mt-1">
            <x-heroicon-s-information-circle class="w-4" />
            {{ $campaign->sends_with_errors_count }} {{ __mc_choice('failed send|failed sends', $campaign->sends_with_errors_count) }}
        </div>
    @endif
</div>
