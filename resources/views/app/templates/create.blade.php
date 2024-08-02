<form
    class="form-grid"
    wire:submit="saveTemplate"
    @keydown.prevent.window.cmd.s="$wire.call('saveTemplate')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveTemplate')"
    method="POST"
>
    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        :placeholder="__mc('Newsletter template')"
        wire:model.lazy="name"
        required
    />

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create template')" />
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-template' })" />
    </div>
</form>
