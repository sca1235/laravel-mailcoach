@php($mail = $getRecord())
<div class="fi-ta-text-item px-3">
    @if($mail->contentItem->click_rate)
        {{ number_format($mail->contentItem->unique_click_count) }}
        <span class="text-xs text-navy-bleak-extra-light">({{ round($mail->contentItem->click_rate / 100) }}%)</span>
    @else
        &ndash;
    @endif
</div>
