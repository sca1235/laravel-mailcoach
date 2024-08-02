@props([
    'type' => 'info',
    'full' => $type === 'help' ? true : false,
    'sync' => false,
    'noIcon' => false,
])

@php($colorClasses = match($type) {
    'error' => 'bg-red-extra-light border border-red-light text-navy',
    'success' => 'bg-green-extra-light border border-green-light text-navy',
    'info' => 'text-blue-dark font-medium',
    'warning' => 'bg-orange-extra-light border border-orange-light text-navy',
    'help' => 'bg-sky-extra-light text-navy border border-sky-light',
    default => '',
})

@php($icon = match($type) {
    'error' => 'heroicon-s-exclamation-circle',
    'success' => 'heroicon-s-check-circle',
    'info' => 'heroicon-s-information-circle',
    'warning' => 'heroicon-s-exclamation-triangle',
    'help' => 'heroicon-s-information-circle',
    default => '',
})

@php($iconClass = match($type) {
    'error' => 'text-red',
    'success' => 'text-green',
    'info' => 'text-blue-dark',
    'warning' => 'text-orange',
    'help' => 'text-blue-dark',
    default => '',
})

<div
    class="
        rounded-lg flex items-start text-xs sm:text-base {{ $type === 'info' ? 'gap-x-3' : 'gap-x-3 sm:gap-x-5 px-5 md:px-6 py-4 md:py-4.5' }} leading-none gap-4
        {{ $colorClasses }}
        {{ $full ? '' : 'md:max-w-2xl' }}
        {{ $attributes->get('class') }}
    "
    {{ $attributes->except('class') }}
>
    @unless($noIcon)
        @if ($sync)
            <x-heroicon-s-arrow-path class="w-5 flex-shrink-0 animate-spin {{ $iconClass }}" />
        @else
            <x-icon :name="$icon" class="w-5 flex-shrink-0 {{ $iconClass }}" />
        @endif
    @endunless
    <div class="markup markup-links markup-code leading-tight max-w-none w-full">
        {{ $slot }}
    </div>
</div>
