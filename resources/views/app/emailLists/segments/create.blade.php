<form
    class="form-grid"
    wire:submit="saveSegment"
    @keydown.prevent.window.cmd.s="$wire.call('saveSegment')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveSegment')"
    method="POST"
>
    @csrf
    <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model.lazy="name" required />

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create segment')" />
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-segment' })" />
    </div>
</form>
