@props([
    'buttons' => false,
    'class' => '',
])

<div
    @if ($buttons)
    x-data="{
        stuck: false,
    }"
    x-intersect:enter.threshold.full="stuck = false"
    x-intersect:leave.threshold.full="stuck = true"
    :class="stuck ? 'card-buttons-stuck' : ''"
    @endif
    class="{{ $buttons ? 'card-buttons' : 'card form-grid' }} {{ $class }}"
    {{ $attributes->except('class') }}
>
    {{ $slot }}
</div>
