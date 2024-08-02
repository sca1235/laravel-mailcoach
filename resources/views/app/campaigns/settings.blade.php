@php
    $linkDescriptions = [];

    if ($this->campaign->emailList?->websiteEnabled()) {
        $linkDescriptions[] = '<a target=_blank href="' . $this->campaign->emailList->websiteUrl() . '">the public website</a>';
    }

    if ($this->campaign->emailList?->campaigns_feed_enabled) {
        $linkDescriptions[] = 'the RSS feed';
    }

    $linkDescriptions = collect($linkDescriptions)->join(', ', ' and ');
@endphp

<form
    class="card-grid"
    method="POST"

    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    @csrf

    <x-mailcoach::fieldset card :legend="__mc('Campaign')" :description="__mc('General information about your campaign')">
        <x-mailcoach::text-field :label="__mc('Name')" name="form.name" wire:model="form.name" required :disabled="!$campaign->isEditable()" />
    </x-mailcoach::fieldset>

    @if ($campaign->isEditable())
        @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $campaign, 'wiremodel' => 'form'])
    @else
        <x-mailcoach::fieldset card :legend="__mc('Audience')">
            <div>
                @if($campaign->emailList)
                    Sent to list <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}"><strong>{{ $campaign->emailList->name }}</strong></a>
                @else
                    Sent to list <strong>{{ __mc('deleted list') }}</strong>
                @endif

            @if($campaign->tagSegment)
                , used segment <strong>{{ $campaign->tagSegment->name }}</strong>
            @endif
            </div>
        </x-mailcoach::fieldset>
    @endif

    <x-mailcoach::fieldset card :legend="__mc('Sender')">
        @if ($campaign->isEditable() && $campaign->emailList)
        <x-mailcoach::alert type="help" class="-mt-4">{!! __mc('Leave empty to use your <a href=":url">email list defaults</a>', ['url' => route('mailcoach.emailLists.general-settings', $campaign->emailList)]) !!}</x-mailcoach::alert>
        @endif
        <div class="grid sm:grid-cols-2 gap-6">
            <x-mailcoach::text-field :label="__mc('From email')" name="form.from_email" wire:model="form.from_email"
                                     type="email" :placeholder="$campaign->emailList?->default_from_email" :disabled="!$campaign->isEditable()" />

            <x-mailcoach::text-field :label="__mc('From name')" name="form.from_name" wire:model="form.from_name" :placeholder="$campaign->emailList?->default_from_name" :disabled="!$campaign->isEditable()"/>

            <x-mailcoach::text-field
                :label="__mc('Reply-to email')"
                name="form.reply_to_email"
                wire:model="form.reply_to_email"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                :placeholder="$campaign->emailList?->default_reply_to_email"
                :disabled="!$campaign->isEditable()"
            />

            <x-mailcoach::text-field
                :label="__mc('Reply-to name')"
                name="form.reply_to_name"
                wire:model="form.reply_to_name"
                :placeholder="$campaign->emailList?->default_reply_to_name"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                :disabled="!$campaign->isEditable()"
            />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        @php([$openTracking, $clickTracking] = $campaign->tracking())
        @if (!is_null($openTracking) || !is_null($clickTracking))
        @php($mailerModel = $campaign->getMailer())
            <x-mailcoach::alert type="help">
                {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the :mailer mailer. <a class="underline !text-blue-dark font-medium" href=":mailerLink">Manage settings.</a>', ['mailer' => $mailerModel->name, 'mailerLink' => route('mailers.edit', $mailerModel)]) !!}

                <div class="flex items-center gap-x-6 mt-4">
                    <x-mailcoach::health-label class="text-sm text-navy-dark" warning reverse :test="$openTracking" :label="$openTracking ? __mc('Open tracking is enabled') : __mc('Open tracking is disabled')" />
                    <x-mailcoach::health-label class="text-sm text-navy-dark" warning reverse :test="$clickTracking" :label="$clickTracking ? __mc('Click tracking is enabled') : __mc('Click tracking is disabled')" />
                </div>
            </x-mailcoach::alert>
        @elseif($campaign->emailList?->campaign_mailer)
            <x-mailcoach::alert type="help">
                {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->emailList->campaign_mailer]) !!}
                <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="underline !text-blue-dark font-medium">Manage settings.</a>
            </x-mailcoach::alert>
        @else
            <x-mailcoach::alert type="help">
                {!! __mc('Your email list does not have a mailer set up yet.') !!}
            </x-mailcoach::alert>
        @endif

        <div class="form-field">
            <label class="label">{{ __mc('Subscriber tags') }}</label>
            <div class="grid gap-3">
                <x-mailcoach::checkbox-field :label="__mc('Add tags to subscribers for opens & clicks')" name="form.add_subscriber_tags" wire:model.live="form.add_subscriber_tags" :disabled="!$campaign->isEditable()" />
                <x-mailcoach::checkbox-field :label="__mc('Add individual link tags')" name="form.add_subscriber_link_tags" wire:model.live="form.add_subscriber_link_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        @if ($form->add_subscriber_tags || $form->add_subscriber_link_tags)
            <div class="max-w-full overflow-x-auto border-l-2 border-blue-dark pl-6">
                <x-mailcoach::alert type="help" max-width="2xl">
                    @if ($form->add_subscriber_tags)
                        <p class="text-sm mb-6">{{ __mc('The following tags will automatically get added to subscribers that open or click the campaign:') }}</p>
                        <x-mailcoach::code-copy class="flex justify-between mb-2.5" code='{{ "campaign-{$campaign->uuid}-opened" }}' />
                        <x-mailcoach::code-copy class="flex justify-between" code='{{ "campaign-{$campaign->uuid}-clicked" }}' />
                    @endif
                    @if ($form->add_subscriber_link_tags)
                        <p class="text-sm @if ($form->add_subscriber_tags) mt-6 @endif mb-0">{{ __mc('Subscribers will receive a unique tag per link clicked.') }}</p>
                    @endif
                </x-mailcoach::alert>
            </div>
        @endif

        <div class="form-field">
            <label class="label">
                {{ __mc('UTM tags') }}
            </label>
            <div class="grid gap-3">
                <x-mailcoach::checkbox-field :label="__mc('Automatically add UTM tags')" name="form.utm_tags" wire:model.live="form.utm_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        @if ($form->utm_tags)
            <div class="border-l-2 border-blue-dark pl-6">
                <div class="grid grid-cols-2 gap-6">
                    <x-mailcoach::text-field :label="__mc('utm_source')" name="form.utm_source" wire:model="form.utm_source"/>
                    <x-mailcoach::text-field :label="__mc('utm_medium')" name="form.utm_medium" wire:model="form.utm_medium"/>
                    <x-mailcoach::text-field :label="__mc('utm_campaign')" name="form.utm_campaign" wire:model="form.utm_campaign"/>
                </div>
                <x-mailcoach::alert type="info" class="mt-6">{{ __mc('UTM Tags will automatically get added to all links in your campaign') }}</x-mailcoach::alert>
            </div>
        @endif
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Publish campaign')">
        @if($this->campaign->emailList?->has_website || $this->campaign->emailList?->campaigns_feed_enabled)
            <div>
                <x-mailcoach::alert type="help">
                    {!! __mc('When this campaign has been sent, we can display the content on :link for this email list', ['link' => $linkDescriptions]) !!}
                </x-mailcoach::alert>
            </div>

            <div class="form-field">
                <div class="grid gap-3">
                    <x-mailcoach::checkbox-field :label="__mc('Show publicly')" name="form.show_publicly" wire:model="form.show_publicly" />
                </div>
            </div>
        @endif

            <x-mailcoach::alert type="info">
                {!! __mc('Webview allows users to view the content of the email in a browser.') !!}
            </x-mailcoach::alert>

        <div class="form-field">
            <div class="grid gap-3">
                <x-mailcoach::checkbox-field :label="__mc('Enable webview')" name="form.enable_webview" wire:model="form.enable_webview" />
            </div>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::api-card
        resource-name="campaign uuid"
        resource="campaign"
        :uuid="$campaign->uuid"
    />

    <x-mailcoach::card class="flex items-center gap-6" buttons>
        <x-mailcoach::button :label="__mc('Save settings')" />
        @if ($form->dirty)
            <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
        @else
            <div wire:key="dirty" wire:dirty>
                <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
            </div>
        @endif
    </x-mailcoach::card>
</form>
