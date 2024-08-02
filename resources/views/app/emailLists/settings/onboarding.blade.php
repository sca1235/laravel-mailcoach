<form
    method="POST"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <div class="card-grid">
        <x-mailcoach::fieldset card :legend="__mc('Subscriptions')">
            <x-mailcoach::alert type="info">
                {!! __mc('Learn more about <a href=":link" target="_blank">subscription settings and forms</a>.', ['link' => 'https://mailcoach.app/resources/learn-mailcoach/features/email-lists#content-forms']) !!}
            </x-mailcoach::alert>

            <div class="form-field max-w-full">
                <div class="grid gap-3">
                    <x-mailcoach::checkbox-field
                        :label="__mc('Require confirmation')"
                        name="form.requires_confirmation"
                        wire:model.live="form.requires_confirmation"
                    />

                    <x-mailcoach::checkbox-field
                        :label="__mc('Allow POST from an external form')"
                        name="form.allow_form_subscriptions"
                        wire:model.live="form.allow_form_subscriptions"
                    />

                    @if($form->allow_form_subscriptions)
                        <div class="pl-8 w-full max-w-full overflow-hidden">
                            <x-mailcoach::code-copy class="input p-0" button-class="w-full text-right mt-4 mr-4" button-position="top" lang="html" :code="$form->getSubscriptionFormHtml()"/>
                        </div>
                    @endif
                </div>
            </div>

            @if($form->allow_form_subscriptions)
                <div class="pl-8 max-w-xl">
                    <x-mailcoach::tags-field
                        :label="__mc('Optionally, allow following subscriber tags')"
                        name="allowed_form_subscription_tags"
                        :value="$form->allowed_form_subscription_tags"
                        :tags="$emailList->tags->pluck('name')->unique()->toArray()"
                    />
                </div>
                <div class="pl-8 max-w-xl">
                    <x-mailcoach::text-field
                        :label="__mc('Optionally, allow following subscriber extra Attributes')"
                        :placeholder="__mc('Attribute(s) comma separated: field1,field2')"
                        name="form.allowed_form_extra_attributes"
                        wire:model.blur="form.allowed_form_extra_attributes"
                    />
                </div>
                <div class="pl-8 max-w-xl">
                    <x-mailcoach::text-field
                        :label="__mc('Honeypot field')"
                        placeholder="honeypot"
                        name="form.honeypot_field"
                        wire:model.blur="form.honeypot_field"
                    />
                </div>
            @endif
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__mc('Landing Pages')">
            <x-mailcoach::alert type="info">
                {!! __mc('Leave empty to use the defaults. <a target="_blank" href=":link">Example</a>', ['link' => route("mailcoach.landingPages.example")]) !!}
            </x-mailcoach::alert>

            @if ($form->requires_confirmation)
                <x-mailcoach::text-field
                    :label="__mc('Confirm subscription')"
                    placeholder="https://"
                    name="form.redirect_after_subscription_pending"
                    wire:model.lazy="form.redirect_after_subscription_pending"
                    type="text"
                />
            @endif
            <x-mailcoach::text-field
                :label="__mc('Someone subscribed')"
                placeholder="https://"
                name="form.redirect_after_subscribed"
                wire:model.lazy="form.redirect_after_subscribed"
                type="text"
            />
            <x-mailcoach::text-field
                :label="__mc('Email was already subscribed')"
                placeholder="https://"
                name="form.redirect_after_already_subscribed"
                wire:model.lazy="form.redirect_after_already_subscribed"
                type="text"
            />
            <x-mailcoach::text-field
                :label="__mc('Someone unsubscribed')"
                placeholder="https://"
                name="form.redirect_after_unsubscribed"
                wire:model.lazy="form.redirect_after_unsubscribed"
                type="text"
            />
        </x-mailcoach::fieldset>

        @if ($form->requires_confirmation)
            <x-mailcoach::fieldset card :legend="__mc('Confirmation mail')">
                @if(empty($emailList->confirmation_mailable_class))
                    <div class="grid gap-3 items-start">
                        <x-mailcoach::radio-field
                            name="confirmation_mail"
                            option-value="send_default_confirmation_mail"
                            :label="__mc('Send default confirmation mail')"
                            wire:model.live="form.confirmation_mail"
                        />
                        <x-mailcoach::radio-field
                            name="confirmation_mail"
                            option-value="send_custom_confirmation_mail"
                            :label="__mc('Send customized confirmation mail')"
                            wire:model.live="form.confirmation_mail"
                        />
                    </div>

                    @if ($form->confirmation_mail === 'send_custom_confirmation_mail')
                        <div class="form-grid">
                            @if (count($transactionalMailTemplates))
                                <div class="flex items-center gap-x-2 max-w-sm">
                                    <div class="w-full">
                                        <x-mailcoach::select-field
                                            wire:model="form.confirmation_mail_id"
                                            name="form.confirmation_mail_id"
                                            :options="$transactionalMailTemplates"
                                            :placeholder="__mc('Select a transactional mail template')"
                                        />
                                    </div>
                                    @if ($emailList->confirmationMail)
                                        <a href="{{ route('mailcoach.transactionalMails.templates.edit', $emailList->confirmationMail) }}" class="link">{{ __mc('Edit') }}</a>
                                    @endif
                                </div>
                            @else
                                <x-mailcoach::alert type="info">
                                    {!! __mc('You need to create a transactional mail template first. <a href=":createLink" class="link">Create one here</a>', [
                                        'createLink' => route('mailcoach.transactional'),
                                    ]) !!}
                                </x-mailcoach::alert>
                            @endif

                            <x-mailcoach::alert type="help" class="markup-code lg:max-w-3xl">
                                {{ __mc('You can use the following placeholders in the subject and body of the confirmation mail:') }}
                                <dl class="mt-4 markup-dl">
                                    <dt><code>::confirmUrl::</code></dt>
                                    <dd>{{ __mc('The URL where the subscription can be confirmed') }}</dd>
                                    <dt><code>::subscriber.first_name::</code></dt>
                                    <dd>{{ __mc('The first name of the subscriber') }}</dd>
                                    <dt><code>::list.name::</code></dt>
                                    <dd>{{ __mc('The name of this list') }}</dd>
                                </dl>
                            </x-mailcoach::alert>
                        </div>
                    @endif
                @else
                    <x-mailcoach::alert type="info">
                        {{ __mc('A custom mailable (:mailable) will be used.', ['mailable' => $emailList->confirmation_mailable_class]) }}
                    </x-mailcoach::alert>
                @endif
            </x-mailcoach::fieldset>
        @endif

        <x-mailcoach::fieldset card :legend="__mc('Welcome Mail')">
            <x-mailcoach::alert type="help">
                {!! __mc('Check out the <a href=":docsUrl" class="link">documentation</a> to learn how to set up a welcome automation.', [
                    'docsUrl' => 'https://mailcoach.app/docs/cloud/using-mailcoach/automations/creating-automation'
                ]) !!}
            </x-mailcoach::alert>
        </x-mailcoach::fieldset>

        <x-mailcoach::card class="flex items-center gap-6" buttons>
            <x-mailcoach::button :label="__mc('Save')" />
            @if ($form->dirty)
                <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
            @else
                <div wire:key="dirty" wire:dirty>
                    <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
                </div>
            @endif
        </x-mailcoach::card>
    </div>
</form>
