<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <x-mailcoach::alert type="success">
            <p>
                Your Mailgun account has been set up. We highly recommend sending a small test campaign to yourself to check if
                everything is working as expected.
            </p>
        </x-mailcoach::alert>

        <table>
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Domain')"
                :value="$mailer->get('domain')"
            />
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Endpoint')"
                :value="$mailer->get('baseUrl')"
            />
            <x-mailcoach::checklist-item
                :label="__mc('Open tracking')"
                :test="$mailer->get('open_tracking_enabled')"
                :value="$mailer->get('open_tracking_enabled') ? __mc('Enabled') : __mc('Disabled')"
            />
            <x-mailcoach::checklist-item
                :label="__mc('Click tracking')"
                :test="$mailer->get('click_tracking_enabled')"
                :value="$mailer->get('click_tracking_enabled') ? __mc('Enabled') : __mc('Disabled')"
            />
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Throttling')"
            >
                <x-slot:value>
                    <p><strong>{{ $mailer->get('mails_per_timespan', 10) }}</strong> {{ __mc('mails every') }} <strong>{{ $mailer->get('timespan_in_seconds', 1) }}</strong> {{ __mc_choice('second|seconds', $mailer->get('timespan_in_seconds', 1)) }}</p>
                </x-slot:value>
            </x-mailcoach::checklist-item>
        </table>
    </x-mailcoach::fieldset>

    @include('mailcoach::app.configuration.mailers.partials.mailerName')

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Send test email')" x-on:click.prevent="$dispatch('open-modal', { id: 'send-test' })" />
    </x-mailcoach::card>
</div>
