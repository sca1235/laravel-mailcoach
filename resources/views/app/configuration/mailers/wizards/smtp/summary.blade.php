<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <x-mailcoach::alert type="success">
            <p>
                Your SMTP mailer has been set up. We highly recommend sending a small test campaign to yourself to check if
                everything is working as expected.
            </p>
        </x-mailcoach::alert>
        <table>
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Host')"
                :value="$mailer->get('host')"
            />
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Port')"
                :value="$mailer->get('port')"
            />
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Username')"
                :value="$mailer->get('username')"
            />
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('Encryption')"
                :value="$mailer->get('encryption') === '' ? 'None' : $mailer->get('encryption')"
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
    <x-mailcoach::button class="mt-4" :label="__mc('Send test email')" x-on:click.prevent="$dispatch('open-modal', { id: 'send-test' })" />
    </x-mailcoach::card>
</div>
