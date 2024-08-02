<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>

    <x-mailcoach::alert type="help">
        <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
        <p>You can find more info about sending limits in <a href="https://docs.aws.amazon.com/ses/latest/dg/manage-sending-quotas.html" target="_blank">the Amazon SES documentation</a>.</p>
    </x-mailcoach::alert>

    <x-mailcoach::alert type="warning">
        When your account is in sandbox mode, the maximum amount of emails you can send is 1 / second, once your account is out of sandbox mode make sure to update the throttling config, you'll find the limit in your SES Account Dashboard
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
                <x-mailcoach::button :label="__mc('Save')"/>
        </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>
</div>
