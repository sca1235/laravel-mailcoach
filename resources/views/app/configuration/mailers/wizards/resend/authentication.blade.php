<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

<x-mailcoach::card>

    <x-mailcoach::alert type="help">
        <p>
        To be able to send mails through Resend, we need to authenticate with the API. Make sure <a href="https://resend.com" target="_blank">you have an account</a>.
        </p>
        <p>
        Next, <a target="_blank" href="https://resend.com/api-keys">create an API key</a>.
        </p>
    </x-mailcoach::alert>

    <form class="form-grid" wire:submit="submit">
        <x-mailcoach::text-field
            wire:model.lazy="apiKey"
            :label="__mc('API Key')"
            :help="__mc('You can find it <a class=\'link\' href=\':url\'>in your API Keys screen</a>', ['url' => 'https://resend.com/api-keys'])"
            name="apiKey"
            type="text"
            autocomplete="off"
        />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Verify')" wire:loading.attr="disabled"/>
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::card>
</div>
