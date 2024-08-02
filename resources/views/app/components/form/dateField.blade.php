@props([
    'minDate' => now()->format('Y-m-d'),
    'maxDate' => null,
    'label' => null,
    'required' => false,
    'name' => null,
    'class' => '',
    'inputClass' => '',
    'value' => '',
    'placeholder' => '',
    'disabled' => false,
])
<div
    class="form-field {{ $class }}"
>
    @if($label)
    <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}
    </label>
    @endif

    <input
        type="date"
        name="{{ $name }}"
        id="{{ $name }}"
        class="input max-w-xs {{ $inputClass }}"
        value="{{ old($name, $value) }}"
        @if ($minDate) min="{{ $minDate }}" @endif
        @if ($maxDate) max="{{ $maxDate }}" @endif
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @disabled($disabled)
    >

    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
