<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <x-mailcoach::alert type="success">
            <p>
                Your Resend account has been set up. We highly recommend sending a small test campaign to yourself to check if
                everything is working as expected.
            </p>
        </x-mailcoach::alert>

        <table>
            @foreach ($domains as $domain)
                <x-mailcoach::checklist-item
                    :label="$domain['name']"
                    :test="$domain['status'] === 'verified'"
                >
                    <x-slot:value>
                        {{ $domain['status'] }} - {{ $domain['region'] }} - <a class="inline-flex gap-1" href="https://resend.com/domains/{{ $domain['id'] }}" target="_blank">Manage <x-heroicon-c-arrow-top-right-on-square class="w-4" /></a>
                    </x-slot:value>
                </x-mailcoach::checklist-item>
            @endforeach
            <x-mailcoach::checklist-item
                :label="__mc('Tracking')"
                neutral
                :value="__mc('Open & click tracking is managed in your domain settings in Resend')"
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
