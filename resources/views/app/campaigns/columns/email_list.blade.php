@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex items-center gap-1.5 px-3">
    @if (! $campaign->emailList)
        &ndash;
    @else
        <p class="link">{{ $campaign->emailList->name }}</p>
        @if($campaign->usesSegment())
            <div class="ml-1 bg-sky-extra-light px-2.5 py-1.5 leading-none rounded-full font-medium text-2xs">
                {{ $campaign->getSegment()->description() }}
            </div>
        @endif
    @endif
</div>
