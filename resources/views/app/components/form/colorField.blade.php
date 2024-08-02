<div class="form-field" wire:ignore>
    @if($label ?? null)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="color-{{ $name }}">
            {{ $label }}
        </label>
    @endif

    <!-- search is a prefix here because otherwise 1Password shows its widget -->
    <input
        x-data
        x-init="() => Coloris({ el: '#search-{{ $name }}', alpha: false, format: 'hsl' });"
        autocomplete="off"
        @if (! $attributes->has('x-bind:type'))
            type="{{ $type ?? 'text' }}"
        @endif
        name="search-{{ $name }}"
        id="search-{{ $name }}"
        class="input font-mono text-sm {{ $inputClass ?? '' }}"
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
