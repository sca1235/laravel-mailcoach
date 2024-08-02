<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::alert type="help">
        <p>
        To be able to send mails through SendGrid, we should authenticate at Sendgrid.
        </p>
            <p>
            You should first <a href="https://sendgrid.com" target="_blank">create an account</a> at SendGrid.
            </p>
                <p>
            Next, <a target="_blank" href="https://mailcoach.app/resources/learn-mailcoach/getting-started/configuring-mail-providers#content-sendgrid">create and API key at SendGrid</a>. Make sure it has the "Mail Send", "Mail Settings" and "Tracking" permissions.
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
