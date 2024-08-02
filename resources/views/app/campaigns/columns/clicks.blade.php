@php(
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = $getRecord()
)

<div class="fi-ta-text-item px-3">
    @if($campaign->clickCount())
        {{ number_format($campaign->uniqueClickCount()) }}
        <span class="text-xs text-navy-bleak-extra-light">({{ round($campaign->clickRate() / 100) }}%)</span>
    @else
        &ndash;
    @endif
</div>
