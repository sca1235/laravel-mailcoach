@php($automation = $getRecord())

<a x-data class="fi-ta-text-item inline-flex items-center gap-1.5 pl-5" href="{{ route('mailcoach.automations.run', $automation) }}">
    @if($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::Paused)
        <x-heroicon-s-pencil-square x-tooltip="'{{ __mc('Draft') }}'" class="text-sand-dark w-5" />
    @else
        <x-heroicon-s-arrow-path x-tooltip="'{{ __mc('Running') }}'" class="w-5 animate-spin" />
    @endif
</a>
