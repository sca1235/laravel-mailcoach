<form
    class="form-grid"
    wire:submit="saveList"
    @keydown.prevent.window.cmd.s="$wire.call('saveList')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveList')"
    method="POST"
>
    <x-mailcoach::text-field :label="__mc('Name')"  wire:model.lazy="name" name="name" :placeholder="__mc('Subscribers')" required />
    <x-mailcoach::text-field :label="__mc('From email')" :placeholder="auth()->guard(config('mailcoach.guard'))->user()->email" wire:model.lazy="default_from_email" name="default_from_email" type="email" required />
    <x-mailcoach::text-field :label="__mc('From name')" :placeholder="auth()->guard(config('mailcoach.guard'))->user()->name" wire:model.lazy="default_from_name" name="default_from_name" />

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create list')" />
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-list' })" />
    </div>
</form>
