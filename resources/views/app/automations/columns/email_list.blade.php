@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex items-center gap-1.5 px-3">
    @if (! $campaign->emailList)
        &ndash;
    @else
        <p class="link">{{ $campaign->emailList->name }}</p>
        @if($campaign->usesSegment())
            <div class="td-secondary-line">
                {{ $campaign->getSegment()->description() }}
            </div>
        @endif
    @endif
</div>
