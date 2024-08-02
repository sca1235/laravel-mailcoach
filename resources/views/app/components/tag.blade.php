@props([
    'neutral' => false,
    'size' => 'sm'
])
<span class="
    inline-flex items-center rounded-full
    no-underline text-navy font-medium
    {{ match($size) {
        '2xs' => 'text-2xs py-1 px-2',
        'xs' => 'text-xs px-2 py-1',
        'sm' => 'text-sm px-4 py-2',
        'md' => 'text-base px-4 py-2',
        'lg' => 'text-lg px-4 py-2',
    } }}
    {{ $neutral
        ? 'bg-sky-extra-light'
        : 'bg-sky-light'
    }}
    {{ $attributes->get('class') }}
" {{ $attributes->except('class') }}>
    {{ $slot }}
</span>
