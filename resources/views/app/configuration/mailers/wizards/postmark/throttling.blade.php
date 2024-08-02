<div class="card-grid">
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>

    <x-mailcoach::alert type="help">
        <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
        <p>Postmark does not have a specific rate limit for emails, but a concurrency limit. We've set this to sane values by default, but you can tweak these if you want to.</p>
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
