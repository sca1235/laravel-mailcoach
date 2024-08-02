@php
    $id = $name . '-' . $optionValue;
    $checked = old($name, $value ?? null) == $optionValue;
@endphp

<label class="grid justify-start grid-cols-auto grid-flow-col gap-3 min-h-0 cursor-pointer" for="{{ $id }}">
    <input
        type="radio"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $optionValue }}"
        class="
            w-6 h-6 border-none appearance-none peer
            before:absolute before:top-0 before:left-0 before:w-6 before:h-6 before:bg-white before:border-2 before:border-sand-bleak before:rounded-full before:overflow-hidden
            after:absolute after:top-[0.45em] after:left-[0.45em] after:w-[0.6em] after:h-[0.6em] after:rounded-full after:bg-blue after:transition-all after:duration-300 after:scale-0
            focus:outline-none focus:ring-transparent
            hover:outline-none hover:ring-transparent
            hover:before:border-blue
            focus:before:border-blue focus:before:bg-white
            checked:text-transparent
            checked:before:border-blue
            checked:after:scale-100
            disabled:opacity-50
        "
        @if($checked) checked @endif
        @if($disabled ?? false) disabled="disabled" @endif
        @if($readOnly ?? false) readonly="readonly" @endif
        {{ $attributes }}
    >
    <span class="peer-checked:text-blue-dark peer-disabled:opacity-50">{{ $label }}</span>
</label>
