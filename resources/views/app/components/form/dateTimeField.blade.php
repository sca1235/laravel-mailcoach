<div class="w-full">
    <div class="flex items-center w-full">
        <x-mailcoach::date-field
            :name="$name . '[date]'"
            wire:model="{{ $name }}.date"
            :value="$value->format('Y-m-d')"
            class="w-full"
            required
        />
        <span class="mx-6">at</span>
        <x-mailcoach::select-field
            :name="$name . '[hours]'"
            wire:model="{{ $name }}.hours"
            :options="$hourOptions"
            :value="$value->format('H')"
            class="w-full"
            required
        />
        <span class="mx-6">:</span>
        <x-mailcoach::select-field
            :name="$name . '[minutes]'"
            wire:model="{{ $name }}.minutes"
            :options="$minuteOptions"
            :value="$value->format('i')"
            class="w-full"
            required
        />
    </div>
    @error($name)
        <p class="form-error mb-1" role="alert">{{ $message }}</p>
    @enderror
</div>
