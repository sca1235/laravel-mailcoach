<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Remove tags') }}
        <span class="form-legend-accent">
            {{ $tags }}
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12">
            <x-mailcoach::text-field
                id="tags"
                :label="__mc('Tags to remove')"
                :required="true"
                name="tags"
                wire:model="tags"
            />
        </div>
    </x-slot>

</x-mailcoach::automation-action>
