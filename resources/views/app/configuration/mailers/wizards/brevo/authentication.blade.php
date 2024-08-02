<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::alert type="help">
        <p>
        To be able to send mails through Brevo, we should authenticate at Brevo.
        </p>
            <p>
            You should first <a href="https://www.brevo.com/" target="_blank">create an account</a> at Brevo.
            </p>
                <p>
            Next, <a target="_blank" href="https://app.brevo.com/settings/keys/api">create an API key at Brevo</a>.
            </p>
    </x-mailcoach::alert>

    <form class="form-grid" wire:submit="submit">
        <x-mailcoach::text-field
            wire:model.defer="apiKey"
            :label="__mc('API Key')"
            name="apiKey"
            type="text"
            autocomplete="off"
        />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Verify')"/>
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::card>
</div>
