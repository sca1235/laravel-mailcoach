@php($campaign = $getRecord())
<div class="fi-ta-text-item px-3 tabular-nums">
    @if($campaign->isSent())
        {{ optional($campaign->sent_at)->toMailcoachFormat() }}
    @elseif($campaign->isSending())
        {{ optional($campaign->updated_at)->toMailcoachFormat() }}
    @elseif($campaign->isScheduled())
        {{ optional($campaign->scheduled_at)->toMailcoachFormat() }}
    @else
        &ndash;
    @endif
</div>
