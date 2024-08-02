<div class="form-field {{ $wrapperClass ?? '' }}">
    @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}

        @if ($help ?? null)
            <span class="ml-1 text-blue-dark opacity-75 cursor-pointer" x-data x-tooltip="@js($help)">
                <x-heroicon-s-question-mark-circle class="w-4" />
            </span>
        @endif
    </label>
    @endif
    <input
        @if (! $attributes->has('autocomplete'))
        autocomplete="off"
        @endif
        @if (! $attributes->has('x-bind:type'))
        type="{{ $type ?? 'text' }}"
        @endif
        name="{{ $name }}"
        id="{{ $name }}"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ old($name, $value ?? '') }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled ?? false) disabled @endif
    >
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
