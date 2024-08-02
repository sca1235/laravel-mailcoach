@props([
    'name',
    'label' => null,
    'type' => 'editor',
])
<div wire:key="{{ $name }}" class="form-field max-w-full" wire:key="{{ $name }}">
    <label class="label" for="field_{{ $name }}">
        {{ $label
            ? \Illuminate\Support\Str::of($label)->trim()->ucfirst()
            : \Illuminate\Support\Str::of($name)->replace('_', ' ')->snake(' ')->trim()->ucfirst()
        }}
    </label>

    @if ($type === 'text')
        <x-mailcoach::text-field
            name="templateFieldValues.{{ $name }}"
            wire:model.lazy="templateFieldValues.{{ $name }}"

        />
    @elseif ($type === 'image')
        <div class="mb-4">
            <x-mailcoach::image-upload
                wire:model="templateFieldValues.{{ $name }}"
            />
        </div>
    @else
        {!! $editor !!}
    @endif

    @error('templateFieldValues.' . $name)
    <p class="form-error">{{ $message }}</p>
    @enderror
</div>
