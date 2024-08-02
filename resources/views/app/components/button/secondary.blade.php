<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button button-secondary'])->except(['label']) }}
>
    {{ $icon ?? '' }}
    {{ $label ?? $slot ?? __mc('Save')  }}
</button>
