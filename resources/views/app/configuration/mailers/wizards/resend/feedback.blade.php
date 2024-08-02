<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::alert type="help">
        Resend can be configured track bounces and complaints. It will send webhooks to Mailcoach, that will be used to
        automatically unsubscribe people.<br/><br/>Optionally, Resend can also send webhooks to inform Mailcoach of opens and
        clicks.
    </x-mailcoach::alert>

    <x-mailcoach::alert type="warning" full>
        <p>At this time, Resend does not support automatically setting up the Mailcoach webhook. You can create the webhook manually in Resend:</p>
        <p><strong>Url: </strong> <x-mailcoach::code click-to-copy :code="action([\Spatie\Mailcoach\Http\Api\Controllers\Vendor\Resend\ResendWebhookController::class], $this->mailer()->configName())" /></p>
        <p>
            <strong>With the following events:</strong>
        </p>
        <ul>
            <li class="mb-1"><code>email.bounced</code></li>
            <li class="mb-1"><code>email.complained</code></li>
            <li class="mb-1"><code>email.clicked</code> (optional)</li>
            <li><code>email.opened</code> (optional)</li>
        </ul>
    </x-mailcoach::alert>

        <form class="form-grid" wire:submit="configureResend">
            <x-mailcoach::text-field
                name="signingSecret"
                wire:model.lazy="signingSecret"
                :label="__mc('Webhook signing secret')"
                :help="__mc('You can find it in your webhook\'s settings')"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Configure Resend')" wire:loading.attr="disabled"/>
            </x-mailcoach::form-buttons>
        </form>
</x-mailcoach::card>
</div>
