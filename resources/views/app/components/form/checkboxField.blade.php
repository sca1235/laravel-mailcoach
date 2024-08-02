<label class="flex items-center gap-3 text-sm" for="{{ $name }}">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value ?? 1 }}"
        @if(old($name, $checked ?? false)) checked @endif
        @if($disabled ?? false) disabled @endif
        {{ $attributes->class('peer w-6 h-6 border-2 border-sand-bleak rounded-md checked:border-blue hover:border-blue checked:focus:border-blue checked:bg-white checked:hover:bg-white checked:hover:border-blue checked:focus:bg-white') }}
    />
    <span class="peer-checked:text-blue-dark">{{ $label }}</span>
</label>

@error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
@enderror
