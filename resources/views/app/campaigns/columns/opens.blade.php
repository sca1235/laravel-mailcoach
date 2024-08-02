@php(
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = $getRecord()
)
<div class="fi-ta-text-item px-3">
    @if (! $campaign->openCount())
        &ndash;
    @else
        {{ number_format($campaign->uniqueOpenCount()) }}
        <span class="text-xs text-navy-bleak-extra-light">({{ round($campaign->openRate() / 100) }}%)</span>
    @endif
</div>
