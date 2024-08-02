<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::alert type="help">
        Postmark can be configured track bounces and complaints. It will send webhooks to Mailcoach, that will be used to
        automatically unsubscribe people.<br/><br/>Optionally, Postmark can also send webhooks to inform Mailcoach of opens and
        clicks.
    </x-mailcoach::alert>

        <form class="form-grid" wire:submit="configurePostmark">
            <x-mailcoach::checkbox-field
                :label="__mc('Enable open tracking')"
                name="trackOpens"
                wire:model.defer="trackOpens"
            />

            <x-mailcoach::checkbox-field
                :label="__mc('Enable click tracking')"
                name="trackClicks"
                wire:model.defer="trackClicks"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Configure Postmark')" wire:loading.attr="disabled"/>
        </x-mailcoach::form-buttons>
        </form>
</x-mailcoach::card>
</div>
