<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button-link'])->except(['label']) }}
>
    @if ((string) $slot)
        {{ $slot }}
    @else
        {{ $label ?? __mc('Cancel')  }}
    @endif
</button>
