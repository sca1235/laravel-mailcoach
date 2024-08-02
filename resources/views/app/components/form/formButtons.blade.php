<div
    x-data="{
        stuck: $persist(false),
    }"
    x-intersect:enter.threshold.full="stuck = false"
    x-intersect:leave.threshold.full="stuck = true"
    :class="stuck ? 'form-buttons-stuck' : ''"
    class="form-buttons {{ $attributes->get('class') }}" {{ $attributes->except('class') }}
    wire:key="form-buttons-{{ \Illuminate\Support\Str::random() }}"
>
    {{ $slot }}
</div>
