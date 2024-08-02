<form
        class="card-grid"
        method="POST"
        wire:submit="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
        x-data="{ type: @entangle('type') }"
        x-cloak
>
    <x-mailcoach::fieldset card :legend="__mc('General')">
        <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model.lazy="name" required/>
        <x-mailcoach::alert type="help" :full="false">
            {{ __mc('This name is used by the application to retrieve this template. Do not change it without updating the code of your app.') }}
        </x-mailcoach::alert>

        <?php
        $editor = config('mailcoach.content_editor', \Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent::class);
        $editorName = (new ReflectionClass($editor))->getShortName();
        ?>
        <x-mailcoach::select-field
                :label="__mc('Format')"
                name="type"
                wire:model.live="type"
                :options="[
                'html' => 'HTML (' . $editorName . ')',
                'markdown' => 'Markdown',
                'blade' => 'Blade',
                'blade-markdown' => 'Blade with Markdown',
            ]"
        />

        <div x-show="type === 'blade'">
            <x-mailcoach::alert type="warning">
                <p class="text-sm mb-2">{{ __mc('Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::alert>
        </div>

        <div x-show="type === 'blade-markdown'">
            <x-mailcoach::alert type="warning">
                <p class="text-sm mb-2">{{ __mc('Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::alert>
        </div>

        <x-mailcoach::checkbox-field :label="__mc('Store mail')" name="store_mail"
                                     wire:model.lazy="store_mail"/>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        <div class="form-field">
            <x-mailcoach::alert type="help">
                {!! __mc('Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::alert>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::api-card
        resource-name="transactionalMailTemplate uuid"
        resource="transactional email"
        :uuid="$template->uuid"
    />

    <x-mailcoach::card class="flex items-center gap-6" buttons>
        <x-mailcoach::button :label="__mc('Save')" />
        @if ($dirty)
            <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
        @else
            <div wire:key="dirty" wire:dirty>
                <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
            </div>
        @endif
    </x-mailcoach::card>
</form>
