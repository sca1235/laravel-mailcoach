<div class="mt-6 flex flex-col gap-y-6">
    <form
        wire:submit="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
        method="POST"
    >
    <x-mailcoach::card>
        @csrf
        @method('PUT')

        <x-mailcoach::text-field wrapper-class="md:max-w-3xl" :label="__mc('Name')" name="name" wire:model="name" type="name" required />

        <div class="form-field max-w-none">
            <label class="label label-required">
                {{ __mc('Conditions') }}
            </label>
            <livewire:mailcoach::condition-builder :email-list="$emailList" :storedConditions="$segment->stored_conditions->castToArray()" />
        </div>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Save segment')" />
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>
    </form>

    <h2 class="text-xl font-medium mt-12 mb-0">{{ __mc('Population') }}</h2>
    <livewire:mailcoach::segment-subscribers wire:key="{{ \Illuminate\Support\Str::random() }}" :emailList="$emailList" :segment="$segment" />

    <x-mailcoach::api-card
        class="mt-6"
        resource-name="segment uuid"
        resource="segment"
        :uuid="$segment->uuid"
    />
</div>
