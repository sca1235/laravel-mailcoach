<form
    class="card-grid"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::alert type="help" full >
        <p>We will send a request to the URL you specified, when one of these events you specified happens.</p>
        <p>You can view more information on the sent payload and security recommendations <a
                href="https://mailcoach.app/docs/cloud/using-mailcoach/webhooks/webhook-payloads" target="_blank">in our docs</a>.</p>
    </x-mailcoach::alert>

    <x-mailcoach::card>

        <x-mailcoach::checkbox-field :label="__mc('Enabled')" name="form.enabled" wire:model="form.enabled" />

        <x-mailcoach::text-field :label="__mc('Name')" name="form.name" wire:model.lazy="form.name" required />

        <x-mailcoach::text-field :label="__mc('URL')" name="form.url" wire:model.lazy="form.url" required />

        <div class="flex items-center gap-x-2" x-data="{ type: 'password' }" x-cloak>
            <x-mailcoach::text-field x-bind:type="type" :label="__mc('Secret')" name="form.secret" wire:model.lazy="form.secret" required />

            <x-mailcoach::rounded-icon x-show="type === 'password'" x-on:click="type = 'text'" class="hover:bg-blue-dark transition-colors duration-300 cursor-pointer mt-6" icon="heroicon-s-eye" size="md" type="info" />
            <x-mailcoach::rounded-icon x-show="type === 'text'" x-on:click="type = 'password'" class="hover:bg-blue-dark transition-colors duration-300 cursor-pointer mt-6" icon="heroicon-s-eye-slash" size="md" type="info" />
        </div>

        <x-mailcoach::checkbox-field :label="__mc('Use for all lists')" name="form.use_for_all_lists" wire:model.live="form.use_for_all_lists" />

        @if (! $form->use_for_all_lists)
            <div class="form-field">
                <label class=label>{{__mc('Only for these email lists')}}</label>
                <x-mailcoach::select-field
                    name="form.emailLists"
                    :multiple="true"
                    wire:model="form.emailLists"
                    :options="$emailListNames"
                />
            </div>
        @endif

        <x-mailcoach::checkbox-field
            name="form.use_for_all_events"
            :label="__mc('Use for all events')"
            wire:model.live="form.use_for_all_events"
        />
        @if (! $form->use_for_all_events)
            <div class="ml-6">
                @foreach($eventOptions as $event => $name)
                    <div class="mb-4">
                        <x-mailcoach::checkbox-field
                            :name="$event"
                            :value="$event"
                            :label="__mc($name)"
                            wire:model="form.events"
                        />
                    </div>
                @endforeach
            </div>
        @endif

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Save webhook')" />
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>

    <div class="mt-8">
        <h2 class="markup-h2">{{ __mc('Webhook logs') }}</h2>
        <livewire:mailcoach::webhook-logs :webhook="$webhook" />
    </div>
</form>
