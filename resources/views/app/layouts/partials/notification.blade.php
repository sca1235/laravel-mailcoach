@php
    use Illuminate\Support\Arr;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;

    $color = $getColor() ?? $getIconColor() ?? 'gray';
    $isInline = $isInline();
@endphp

<x-filament-notifications::notification
    :notification="$notification"
    x-transition:enter-start="opacity-0 translate-x-12"
    :x-transition:leave-end="
        Arr::toCssClasses([
            'opacity-0 translate-x-12',
            'scale-95' => ! $isInline,
        ])
    "
    @class([
        'w-full transition duration-300',
        ...match ($isInline) {
            true => [],
            false => [
                "bg-navy-bleak text-white rounded py-4 px-6 max-w-sm ring-1 alert-" . $color,
            ],
        },
    ])
>
    <div
        @class([
            'flex items-start w-full gap-3',
            ...match ($isInline) {
                true => [],
                false => [
                    'rounded-xl',
                ],
            },
        ])
    >
        @if ($icon = $getIcon())
            <x-filament-notifications::icon
                :color="$getIconColor()"
                :icon="$icon"
                :size="$getIconSize()"
            />
        @endif

        <div class="grid flex-1 gap-y-1">
            @if (filled($title = $getTitle()))
                <p class="text-sm leading-5">
                    {{ str($title)->sanitizeHtml()->toHtmlString() }}
                </p>
            @endif

            @if (filled($date = $getDate()))
                <x-filament-notifications::date class="mt-1">
                    {{ $date }}
                </x-filament-notifications::date>
            @endif

            @if (filled($body = $getBody()))
                <p class="text-sm leading-5 font-light">
                    {{ str($body)->sanitizeHtml()->toHtmlString() }}
                </p>
            @endif

            @if ($actions = $getActions())
                <x-filament-notifications::actions
                    :actions="$actions"
                    class="mt-3"
                />
            @endif

        </div>

        <x-filament-notifications::close-button />
    </div>

    <div class="
        absolute
        pointer-events-none
        left-0
        bottom-0
        h-1
        left-0
        right-0
        transform
        origin-bottom-left
        shadow-card
        {{ match($color) {
            'success' => 'bg-green',
            'error' => 'bg-red',
            'warning' => 'bg-orange',
            default => 'bg-blue'
        } }}
    " style="animation: alert-countdown var(--alert-duration, 5s) linear forwards; --alert-duration: {{ $getDuration() / 1000 }}s"></div>
</x-filament-notifications::notification>
