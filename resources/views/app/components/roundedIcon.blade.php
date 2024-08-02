@props([
    'type' => 'neutral',
    'size' => 'sm',
    'icon' => 'heroicon-s-information-circle',
    'class' => '',
])
@php
    $typeCss = [
        'success' => 'bg-green text-white',
        'warning' => 'bg-orange text-white',
        'error' => 'bg-red text-white',
        'info' => 'bg-blue text-white',
        'help' => 'bg-blue text-white',
        'neutral' => 'bg-sky-extra-light text-navy',
    ];

    $sizeCss = [
        'sm' => 'w-4 h-4 p-0.5 text-[8px] ',
        'md' => 'h-6 w-6 p-1 text-[10px] ',
        'lg' => 'h-8 w-8 p-2 text-[12px] ',
    ];
@endphp

<span class="
    rounded-full
    inline-flex items-center justify-center tracking-[-1px]
    leading-none
    {{ $sizeCss[$size] }}
    {{ $typeCss[$type] }}
    {{ $class ?? '' }}
" {{ $attributes->except(['class', 'size', 'minimal', 'icon']) }}>
    <x-icon class="{{ $sizeCss[$size] }}" :name="$icon" />
</span>
