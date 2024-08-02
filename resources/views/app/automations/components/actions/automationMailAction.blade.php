<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable"
                                :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Send email') }}
        <span class="form-legend-accent">
            @if ($automation_mail_id)
                @php($automationMail = \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::find($automation_mail_id))
                @if ($automationMail)
                    <a target="_blank" class="inline-flex items-center gap-x-1" href="{{ route('mailcoach.automations.mails.content', $automationMail) }}">
                        {{ optional($automationMail)->name }} <x-heroicon-s-arrow-top-right-on-square class="w-4" />
                    </a>
                @endif
            @endif
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12">
            @if (! $mailOptions)
                <x-mailcoach::alert :type="$errors->has('automation_mail_id') ? 'error' : 'help'">
                    {!! __mc('You need to <a href=":url" wire:navigate>create an automation email</a> before you can send an email.', [
                        'url' => route('mailcoach.automations').'#create-automation-mail'
                    ]) !!}
                </x-mailcoach::alert>
            @else
                <x-mailcoach::select-field
                    :label="__mc('Email')"
                    name="automation_mail_id"
                    wire:model="automation_mail_id"
                    :options="$mailOptions"
                />
            @endif
        </div>
    </x-slot>

</x-mailcoach::automation-action>
