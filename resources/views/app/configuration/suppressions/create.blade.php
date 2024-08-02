<form class="form-grid" wire:submit="save" method="POST">
    @csrf

    <x-mailcoach::text-field type="email" :label="__mc('Email')" wire:model.lazy="email" name="email" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create suppression')" />

        <button type="button" class="button-link" x-on:click="$dispatch('close-modal', { id: 'create-suppression' })">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
