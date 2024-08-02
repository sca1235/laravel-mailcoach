@php($segment = $getRecord())

<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 px-3">
    <livewire:mailcoach::segment-population-count wire:key="segment-{{ $segment->id }}" :segment="$segment" />
</div>
