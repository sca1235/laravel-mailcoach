<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button button-tertiary'])->except(['label']) }}
>
    {{ $icon ?? '' }}
    {{ $label ?? $slot ?? __mc('Save')  }}
</button>
