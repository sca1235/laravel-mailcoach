@php(
    /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $template */
    $template = $getRecord()
)
<div class="fi-ta-text-item px-3">
    @if (! $template->openCount())
        &ndash;
    @else
        {{ number_format($template->uniqueOpenCount()) }}
        <span class="text-xs text-navy-bleak-extra-light">({{ round($template->openRate() / 100) }}%)</span>
    @endif
</div>
