<div>
    <form
            class="card-grid"
            method="POST"
            wire:submit="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
    >
        <x-mailcoach::fieldset card :legend="__mc('Recipients')">
            <x-mailcoach::alert type="help">
                {{ __mc('These recipients will be merged with the ones when the mail is sent. You can specify multiple recipients comma separated.') }}
            </x-mailcoach::alert>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('To')"
                                     name="to" wire:model.lazy="to"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Cc')"
                                     name="cc" wire:model.lazy="cc"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Bcc')"
                                     name="bcc" wire:model.lazy="bcc"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__mc('Email')">

            <x-mailcoach::text-field
                    :label="__mc('Subject')"
                    name="subject"
                    wire:model.lazy="subject"
                    required
            />

            @if ($template->type === 'html')
                @livewire(config('mailcoach.content_editor'), ['model' => $template->contentItem])
            @else
                <x-mailcoach::html-field label="{{ [
                    'markdown' => 'Markdown',
                    'blade' => 'Blade',
                    'blade-markdown' => 'Blade with Markdown',
                ][$template->type] }}" name="html" wire:model.lazy="html"/>
            @endif

            <x-mailcoach::form-buttons>
                <div class="flex gap-x-2 items-center">
                    <x-mailcoach::button
                        @keydown.prevent.window.cmd.s="$wire.call('save')"
                        @keydown.prevent.window.ctrl.s="$wire.call('save')"
                        wire:click.prevent="save"
                        :label="__mc('Save content')"
                    />

                    @if (config('mailcoach.content_editor') !== \Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class)
                        <x-mailcoach::button-secondary
                            x-on:click.prevent="$dispatch('open-modal', { id: 'preview' })"
                            :label="__mc('Preview')"
                        />
                        <template x-teleport="body">
                            <x-mailcoach::preview-modal
                                id="preview"
                                :html="$html"
                                :title="__mc('Preview')"
                            />
                        </template>
                    @endif

                    <div class="ml-4">
                        @if ($dirty)
                            <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
                        @else
                            <div wire:key="dirty" wire:dirty>
                                <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
                            </div>
                        @endif
                    </div>
                </div>
            </x-mailcoach::form-buttons>
        </x-mailcoach::fieldset>
    </form>

    <x-mailcoach::replacer-help-texts :model="$template"/>
</div>
