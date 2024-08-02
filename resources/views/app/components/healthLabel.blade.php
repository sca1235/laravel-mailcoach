@props([
    'reverse' => isset($reverse) && $reverse,
    'warning' => isset($warning) && $warning,
    'neutral' => isset($neutral) && $neutral,
    'test' => false,
    'label' => '',
    'icon' => '',
])
<span class="inline-flex {{ $reverse ? 'md:flex-row-reverse' : '' }} gap-2 items-center {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    @if(isset($label) && $label)
    <span class="
        text-sm font-medium
        {{ match(true) {
            $neutral => 'text-navy-dark',
            (bool) $test => 'text-green-dark',
            !$test && $warning => 'text-orange-dark',
            !$test && !$warning => 'text-red-dark',
            default => '',
        } }}
    ">
        {{ $label }}
    </span>
    @endisset
    @if ($neutral)
        <x-icon :name="$icon ?: 'heroicon-s-information-circle'" class="w-5 text-sky flex-shrink-0" />
    @elseif ($test)
        <x-heroicon-s-check-circle class="w-5 flex-shrink-0 text-green" />
    @elseif($warning)
        <x-heroicon-s-exclamation-triangle class="w-5 flex-shrink-0 text-orange" />
    @else
        <x-heroicon-s-exclamation-circle class="w-5 flex-shrink-0 text-red" />
    @endif
</span>
