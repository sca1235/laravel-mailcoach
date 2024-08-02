<form
    class="card-grid"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"

>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model.lazy="name" required  />
</x-mailcoach::card>

    <x-mailcoach::fieldset card :legend="__mc('Sender')">
        <x-mailcoach::alert type="info" class="-mt-4">{!! __mc('Leave empty to use the defaults from the automation\'s email list. These will also be set the first time the automation mail is sent.') !!}</x-mailcoach::alert>
        <div class="grid grid-cols-2 gap-6">
            <x-mailcoach::text-field :label="__mc('From email')" name="from_email" wire:model.lazy="from_email"
                                     type="email" />

            <x-mailcoach::text-field :label="__mc('From name')" name="from_name" wire:model.lazy="from_name" />

            <x-mailcoach::text-field
                :label="__mc('Reply-to email')"
                name="reply_to_email"
                wire:model.lazy="reply_to_email"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
            />

            <x-mailcoach::text-field
                :label="__mc('Reply-to name')"
                name="reply_to_name"
                wire:model.lazy="reply_to_name"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
            />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        <x-mailcoach::alert type="info">
            {!! __mc('Open & Click tracking are managed by your email provider.') !!}
        </x-mailcoach::alert>

        <div class="form-field">
            <label class="label">{{ __mc('Subscriber Tags') }}</label>
            <div class="grid gap-3">
                <x-mailcoach::checkbox-field :label="__mc('Add tags to subscribers for opens & clicks')" name="add_subscriber_tags" wire:model.live="add_subscriber_tags" />
                <x-mailcoach::checkbox-field :label="__mc('Add individual link tags')" name="add_subscriber_link_tags" wire:model.live="add_subscriber_link_tags" />
            </div>
        </div>

        @if ($add_subscriber_tags || $add_subscriber_link_tags)
            <x-mailcoach::alert type="help" max-width="2xl">
                @if ($add_subscriber_tags)
                    <p class="text-sm mb-2">{{ __mc('The following tags will automatically get added to subscribers that open or click the automation mail:') }}</p>
                    <p x-data="{}">
                        <x-mailcoach::code-copy class="flex gap-x-2 mb-1" code='{{ "automation-mail-{$mail->uuid}-opened" }}' />
                        <x-mailcoach::code-copy class="flex gap-x-2" code='{{ "automation-mail-{$mail->uuid}-clicked" }}' />
                    </p>
                @endif
                @if ($add_subscriber_link_tags)
                    <p class="text-sm">{{ __mc('Subscribers will receive a unique tag per link clicked.') }}</p>
                @endif
            </x-mailcoach::alert>
        @endif

        <div class="form-field">
            <label class="label">{{ __mc('UTM Tags') }}</label>
            <div class="grid gap-3">
                <x-mailcoach::checkbox-field :label="__mc('Automatically add UTM tags')" name="utm_tags" wire:model.live="utm_tags" />
            </div>
        </div>

        @if ($utm_tags)
        <x-mailcoach::alert type="help">
            <p class="text-sm mb-2">{{ __mc('When checked, the following UTM Tags will automatically get added to any links in your automation mail:') }}</p>
            <dl class="markup-dl">
                <dt><strong>utm_source</strong></dt><dd>newsletter</dd>
                <dt><strong>utm_medium</strong></dt><dd>email</dd>
                <dt><strong>utm_campaign</strong></dt><dd>{{ \Illuminate\Support\Str::slug($mail->name) }}</dd>
            </dl>
        </x-mailcoach::alert>
        @endif
    </x-mailcoach::fieldset>

    <x-mailcoach::api-card
        resource-name="automationMail uuid"
        resource="automation email"
        :uuid="$mail->uuid"
    />

    <x-mailcoach::card  buttons>
        <x-mailcoach::button :label="__mc('Save settings')" />
    </x-mailcoach::card>
</form>
