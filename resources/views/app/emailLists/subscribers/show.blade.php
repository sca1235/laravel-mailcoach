<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */ ?>
<div class="card-grid">
    <x-mailcoach::card
        class="
            grid divide-x divide-snow px-2
            {{ match(true) {
                $subscriber->subscribed_at && $subscriber->unsubscribed_at => 'grid-cols-3 2xl:grid-cols-5',
                $subscriber->subscribed_at && !$subscriber->unsubscribed_at => 'grid-cols-2 2xl:grid-cols-4',
                !$subscriber->subscribed_at && !$subscriber->unsubscribed_at => '2xl:grid-cols-3',
            } }}
        "
    >
        <x-mailcoach::statistic
            num-class="text-2xl font-medium"
            :label="$subscriber->created_at->toMailcoachFormat()"
            :stat="__mc('Created at')"
        />

        @if ($subscriber->subscribed_at)
            <x-mailcoach::statistic
                num-class="text-2xl font-medium"
                :label="$subscriber->subscribed_at->toMailcoachFormat()"
                :stat="__mc('Subscribed at')"
            />
        @endif

        @if ($subscriber->unsubscribed_at)
            <x-mailcoach::statistic
                num-class="text-2xl font-medium"
                :label="$subscriber->unsubscribed_at->toMailcoachFormat()"
                :stat="str_replace(' ', '&nbsp;', __mc('Unsubscribed at'))"
            />
        @endif

        <x-mailcoach::statistic
            num-class="text-2xl font-medium"
            :label="__mc('Opens')"
            :stat="$subscriber->opens()->count()"
        />

        <x-mailcoach::statistic
            num-class="text-2xl font-medium"
            :label="__mc('Clicks')"
            :stat="$subscriber->clicks()->count()"
        />
    </x-mailcoach::card>

    <h2 class="text-xl font-medium flex items-center gap-x-3">
        {{ __mc('Details') }}
    </h2>
    <x-mailcoach::card>
        <form
                class="form-grid"
                method="POST"
                wire:submit="save"
                @keydown.prevent.window.cmd.s="$wire.call('save')"
                @keydown.prevent.window.ctrl.s="$wire.call('save')"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__mc('Email')" name="email" wire:model="email" type="email" required/>
            <x-mailcoach::text-field :label="__mc('First name')" name="first_name" wire:model="first_name"/>
            <x-mailcoach::text-field :label="__mc('Last name')" name="last_name" wire:model="last_name"/>
            <x-mailcoach::tags-field
                    :label="__mc('Tags')"
                    name="tags"
                    :value="$tags"
                    :tags="$subscriber->emailList->tags()->where('type', \Spatie\Mailcoach\Domain\Audience\Enums\TagType::Default)->pluck('name')->toArray()"
                    :multiple="true"
                    allow-create
            />

            <div class="form-field">
                <label class="label">{{ __mc('Extra attributes') }}</label>
                <x-mailcoach::alert type="info" class="markup-code mb-4" full>
                    {!! __mc('You can add and remove attributes which can then be used in your campaigns or automations using <code class="font-normal">&#123;&#123;&nbsp;subscriber.&lt;key&gt;&nbsp;&#125;&#125;</code>') !!}
                </x-mailcoach::alert>

                <div x-data="{ attributes: @entangle('extra_attributes').live }">
                    <template x-for="(attribute, index) in attributes" x-bind:key="index">
                        <div class="my-4 flex items-center w-full gap-x-2">
                            <div class="relative w-full flex items-center">
                                <x-mailcoach::text-field wrapper-class="w-full" x-model="attribute.key" name="key"
                                                         :label="__mc('Key')">
                                </x-mailcoach::text-field>
                                <button
                                    type="button"
                                    tabindex="-1"
                                    x-tooltip.on.click="'{{ __mc('Copied!') }}'"
                                    x-clipboard="'@{{ subscriber.' + attribute.key + ' }}'"
                                    class="absolute right-0 mt-8 mr-4 text-sm ml-1 text-gray-500"
                                >
                                    <x-heroicon-s-document-duplicate class="w-4" />
                                </button>
                            </div>
                            <x-mailcoach::text-field
                                wrapper-class="w-full"
                                x-model="attribute.value"
                                name="value"
                                :label="__mc('Value')"
                            />
                            <button
                                x-on:click.prevent="
                                if (confirm(@js(__mc('Are you sure you want to delete this attribute?')))) {
                                    attributes.splice(index, 1)
                                }
                            "
                                class="mt-auto mb-4 pb-px opacity-75 hover:text-red cursor-pointer"
                            >
                                <x-heroicon-s-trash class="w-4" />
                            </button>
                        </div>
                    </template>
                    <div>
                        <x-mailcoach::button-link class="flex items-center gap-x-1" x-on:click.prevent="attributes.push({ key: '', value: '' })">
                            <x-heroicon-s-plus-circle class="w-4" />
                            {{ __mc('Add attribute') }}
                        </x-mailcoach::button-link>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-mailcoach::button class="" :label="__mc('Save subscriber')"/>

                @if ($subscriber->isUnconfirmed())
                    <x-mailcoach::confirm-button
                        on-confirm="() => $wire.call('resendConfirmation')"
                    >
                        <span class="button button-tertiary">
                            {{ __mc('Resend confirmation') }}
                        </span>
                    </x-mailcoach::confirm-button>
                    <x-mailcoach::confirm-button
                        on-confirm="() => $wire.call('confirm')"
                    >
                        <span class="button button-tertiary">
                            {{ __mc('Confirm') }}
                        </span>
                    </x-mailcoach::confirm-button>
                @endif
                @if ($subscriber->isSubscribed())
                    <x-mailcoach::confirm-button
                        on-confirm="() => $wire.call('unsubscribe')"
                    >
                        <span class="button button-tertiary">
                            {{ __mc('Unsubscribe') }}
                        </span>
                    </x-mailcoach::confirm-button>
                @endif
                @if ($subscriber->isUnsubscribed())
                    <x-mailcoach::confirm-button
                        on-confirm="() => $wire.call('resubscribe')"
                    >
                        <span class="button button-tertiary">
                            {{ __mc('Resubscribe') }}
                        </span>
                    </x-mailcoach::confirm-button>
                @endif

                <x-mailcoach::confirm-button
                    danger
                    on-confirm="() => $wire.call('delete')"
                >
                    <span class="button-link text-red hover:text-red-dark">{{ __mc('Delete') }}</span>
                </x-mailcoach::confirm-button>
            </div>
        </form>
    </x-mailcoach::card>

    <div>
        <h2 class="text-xl font-medium mb-6">{{ __mc('Received emails') }}</h2>
        <livewire:mailcoach::subscriber-sends :subscriber="$subscriber" :email-list="$emailList"/>
    </div>

    <x-mailcoach::api-card
        resource-name="subscriber uuid"
        resource="subscriber"
        :uuid="$subscriber->uuid"
    />
</div>
