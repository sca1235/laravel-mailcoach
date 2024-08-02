<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Wait for') }}
        <span class="form-legend-accent">
            {{ $this->description }}
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-8">
            <x-mailcoach::text-field
                :label="__mc('Length')"
                :required="true"
                name="length"
                wire:model.live="length"
                type="number"
            />
        </div>

        <div class="col-span-4">
        <x-mailcoach::select-field
            :label="__mc('Unit')"
            :required="true"
            name="unit"
            wire:model.live="unit"
            :sort="false"
            :options="
                collect($units)
                    ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                    ->toArray()
            "
        />
        </div>
    </x-slot>
</x-mailcoach::automation-action>
