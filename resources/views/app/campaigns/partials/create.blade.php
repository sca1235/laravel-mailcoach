<form
    class="form-grid"
    wire:submit="saveCampaign"
    @keydown.prevent.window.cmd.s="$wire.call('saveCampaign')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveCampaign')"
    method="POST"
>
    @if (count($emailListOptions))
        <x-mailcoach::text-field
            :label="__mc('Name')"
            wire:model.lazy="name"
            name="name"
            :placeholder="__mc('Newsletter #1')"
            required
        />
        <x-mailcoach::select-field
            :label="__mc('Email list')"
            :options="$emailListOptions"
            wire:model.lazy="email_list_id"
            name="email_list_id"
            required
        />

        @if(count($templateOptions) > 1)
            <x-mailcoach::select-field
                :label="__mc('Template')"
                :options="$templateOptions"
                wire:model.lazy="template_id"
                position="top"
                name="template_id"
            />
        @endif

        <div class="flex items-center gap-x-3">
            <x-mailcoach::button :label="__mc('Create campaign')" />
            <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-campaign' })" />
        </div>
    @else
        <div class="flex flex-col items-center gap-6">
            <div class="bg-sand-extra-light rounded-full w-16 h-16 flex items-center justify-center">
                <x-heroicon-s-user-group class="w-8 text-sand" />
            </div>
            <div class="text-center">
                <h2 class="text-xl font-medium mb-2">{{ __mc('No lists') }}</h2>
                <p class="">{{ __mc('You need at least one list to collect subscribers and send out campaigns.') }}</p>
            </div>
            <a href="{{ route('mailcoach.emailLists') }}" wire:navigate>
                <x-mailcoach::button-tertiary>
                    {{ __mc('Go to lists') }}
                </x-mailcoach::button-tertiary>
            </a>
        </div>
    @endif
</form>
