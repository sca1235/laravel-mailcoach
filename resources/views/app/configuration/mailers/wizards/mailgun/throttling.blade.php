<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
    <x-mailcoach::card>
        <x-mailcoach::alert type="help">
            <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
            <p></p>
            <p>You can find more info about sending limits in <a href="https://help.mailgun.com/hc/en-us/articles/115001124753-Account-Sending-Limitation" target="_blank">the Mailgun documentation</a>.</p>
        </x-mailcoach::alert>

        <x-mailcoach::alert type="warning">
            When your Mailgun account is in probation, the maximum amount of emails you can send through them is 100 / hour. Once your Mailgun account is out of probation, a sensible limit is 50 emails per second
        </x-mailcoach::alert>

        <form class="form-grid" wire:submit="submit">
            <div class="flex items-center gap-x-2">
                <span>{{ __mc('Send') }}</span>
                <x-mailcoach::text-field
                    wrapper-class="w-32"
                    wire:model.lazy="mailsPerTimeSpan"
                    label=""
                    name="mailsPerTimeSpan"
                    type="number"
                />
                <span>{{ __mc('mails every') }}</span>
                <x-mailcoach::text-field
                    wrapper-class="w-32"
                    wire:model.lazy="timespanInSeconds"
                    label=""
                    name="timespanInSeconds"
                    type="number"
                />
                <span>{{ __mc_choice('second|seconds', $timespanInSeconds) }}</span>
            </div>

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Save')" wire:loading.attr="disabled"/>
        </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>
</div>
