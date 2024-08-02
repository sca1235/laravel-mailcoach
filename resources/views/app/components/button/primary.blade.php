<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'button'])->except(['label']) }}
>
    {{ $icon ?? '' }}
    {{ $label ?? $slot ?? __mc('Save')  }}
</button>
