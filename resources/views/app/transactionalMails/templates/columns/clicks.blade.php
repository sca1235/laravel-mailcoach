@php(
    /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $template */
    $template = $getRecord()
)

<div class="fi-ta-text-item px-3">
    @if($template->clickCount())
        {{ number_format($template->uniqueClickCount()) }}
        <span class="text-xs text-navy-bleak-extra-light">({{ round($template->clickRate() / 100) }}%)</span>
    @else
        &ndash;
    @endif
</div>
