<form class="form-grid" wire:submit="saveWebhook" method="POST">
    @csrf

    <x-mailcoach::text-field type="name" :label="__mc('Name')" wire:model.lazy="name" name="name" required />
    <x-mailcoach::text-field type="url" :label="__mc('Url')" wire:model.lazy="url" name="url" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create new webhook')" />

        <button type="button" class="button-link" x-on:click="$dispatch('close-modal', { id: 'create-webhook' })">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
