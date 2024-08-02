<form
    class="form-grid"
    wire:submit="saveTag"
    @keydown.prevent.window.cmd.s="$wire.call('saveTag')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveTag')"
    method="POST"
>
    @csrf

    <x-mailcoach::text-field :label="__mc('Name')" wire:model="name" name="name" required />

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create tag')"/>
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-tag' })" />
    </div>
</form>
