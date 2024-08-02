@php(
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = $getRecord()
)
<div class="fi-ta-text-item inline-flex items-center gap-1.5 px-3">
    @if (! $campaign->isCancelled() && $campaign->sentToNumberOfSubscribers())
        {{ number_format($campaign->sentToNumberOfSubscribers()) }}
    @elseif ($sentSendsCount = $campaign->contentItems->sum(fn (\Spatie\Mailcoach\Domain\Content\Models\ContentItem $contentItem) => $contentItem->sent_sends_count))
        {{ number_format($sentSendsCount) }}
    @else
        &ndash;
    @endif
</div>
