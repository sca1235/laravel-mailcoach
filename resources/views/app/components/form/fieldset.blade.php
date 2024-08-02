@props([
    'card' => false,
    'clean' => false,
    'focus' => false,
    'class' => '',
    'legend' => null,
    'description' => null,
])

<fieldset {{ $attributes->except('class') }} class="{{ $card? 'card form-grid' : ($clean? 'form-fieldset-clean' : 'form-fieldset') }} {{ $class }} {{ $focus ? 'card-focus' : '' }}">
    @if($legend || $description)
        <div class="flex flex-col gap-y-3">
            @if ($legend)
                <h3 class="text-xl font-medium">
                    {{ $legend }}
                </h3>
            @endif
            @if($description)
                <p class="text-base">{{ $description }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</fieldset>
