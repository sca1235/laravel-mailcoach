<x-mailcoach::card
    wire:poll.20s="autosave"
    class="flex flex-col gap-y-4"
>
    @foreach ($contentItems as $index => $contentItem)
        @unless($loop->first)
            <hr class="border-t border-snow my-8">
        @endunless
        <div
            wire:key="{{ $contentItem->uuid }}"
        >
            <div x-data="{
                collapsed: false,
            }">
                <div class="flex items-center relative z-10 pointer-events-none" x-bind:class="collapsed ? '' : '{{ $contentItems->count() > 1 ? 'mb-6' : '-mb-6' }}'" x-cloak>
                    @if ($contentItems->count() > 1)
                        <div class="flex items-center gap-x-4 pointer-events-auto">
                            <button type="button" x-tooltip="'{{ __mc('Expand') }}'" x-show="collapsed" x-on:click="collapsed = !collapsed">
                                <x-icon class="w-5 h-5" name="heroicon-o-chevron-up" />
                            </button>
                            <button type="button" x-tooltip="'{{ __mc('Collapse') }}'" x-show="!collapsed" x-on:click="collapsed = !collapsed">
                                <x-icon class="w-5 h-5" name="heroicon-o-chevron-down" />
                            </button>
                            <span class="w-6 h-6 relative rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold bg-sky-extra-light">
                                {{ $index + 1 }}
                            </span>
                            <h3 class="markup-h3 leading-none mb-1">
                                {{ $contentItem->subject }}
                            </h3>
                        </div>
                    @endif
                    <div class="ml-auto flex items-center gap-x-2 pointer-events-auto -mr-1.5">
                        <button class="bg-transparent hover:bg-sand-extra-light p-1.5 rounded-full transition-colors duration-300" type="button" x-tooltip="'{{ __mc('Add split test') }}'" wire:click="addSplitTest('{{ $contentItem->uuid }}')">
                            <x-icon class="w-5 h-5" name="heroicon-o-document-plus" />
                        </button>
                        <button class="bg-transparent hover:bg-sand-extra-light p-1.5 rounded-full transition-colors duration-300" type="button" x-tooltip="'{{ __mc('Preview') }}'" x-on:click.prevent="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })">
                            <x-icon class="w-5 h-5" name="heroicon-o-eye" />
                        </button>
                        <button class="bg-transparent hover:bg-sand-extra-light p-1.5 rounded-full transition-colors duration-300" type="button"
                            x-tooltip="'{{ __mc('Save & send test') }}'"
                            x-on:click="$wire.call('save'); $dispatch('open-modal', { id: 'send-test-{{ $contentItem->uuid }}' })"
                        >
                            <x-icon class="w-5 h-5" name="heroicon-o-paper-airplane" />
                        </button>
                        @if ($contentItems->count() > 1)
                            <x-mailcoach::confirm-button class="bg-transparent hover:bg-sand-extra-light p-1.5 rounded-full transition-colors duration-300" on-confirm="() => $wire.deleteSplitTest('{{ $contentItem->uuid }}')" confirm-text="{{ __mc('Are you sure you want to delete this split test?') }}" x-tooltip="'{{ __mc('Delete variant') }}'">
                                <x-icon class="w-5 h-5 hover:text-red" name="heroicon-o-trash" />
                            </x-mailcoach::confirm-button>
                        @endif
                    </div>
                </div>

                <div class="form-grid" wire:ignore x-show="!collapsed" x-collapse>
                    <form
                        class="card-grid"
                        method="POST"

                        wire:submit="save"
                        @keydown.prevent.window.cmd.s="$wire.call('save')"
                        @keydown.prevent.window.ctrl.s="$wire.call('save')"
                    >
                        @csrf

                        <x-mailcoach::text-field :label="__mc('Subject')" name="subject" wire:model="content.{{ $contentItem->uuid }}.subject" />
                    </form>

                    @livewire(config('mailcoach.content_editor'), [
                        'model' => $contentItem,
                    ])
                </div>
            </div>
        </div>
    @endforeach

    @if ($contentItems->count() > 1)
        <hr class="border-t border-snow my-8">
    @endif

    <x-mailcoach::form-buttons>
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <x-mailcoach::button
                        @keydown.prevent.window.cmd.s="$wire.call('save')"
                        @keydown.prevent.window.ctrl.s="$wire.call('save')"
                        wire:click.prevent="save"
                        :label="__mc('Save content')"
                    />
                </div>

                @if ($contentItems->count() <= 1)
                <x-mailcoach::button-secondary
                    class="!ml-0"
                    x-on:click.prevent="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })"
                    :label="__mc('Preview')"
                />
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-x-6 sm:ml-auto">
                @if ($contentItems->count() <= 1)
                <x-mailcoach::button-link
                    class="!ml-0"
                    x-on:click.prevent="$wire.call('save'); $dispatch('open-modal', { id: 'send-test-{{ $contentItem->uuid }}' })"
                    :label="__mc('Save & send test')"
                />
                @endif

                <x-mailcoach::button-link
                    class="!ml-0"
                    wire:click="addSplitTest('{{ $contentItem->uuid }}')"
                    :label="__mc('Add split test')"
                />

                @isset($contentItem)
                    <x-mailcoach::replacer-help-texts :model="$contentItem" />
                @endisset
            </div>
        </div>
    </x-mailcoach::form-buttons>

    <div>
        @if ($this->autosaveConflict)
            <x-mailcoach::alert type="warning">
                {{ __mc('Autosave disabled, the content was saved somewhere else. Refresh the page to get the latest content or save manually to override.') }}
            </x-mailcoach::alert>
        @else
            <p class="text-xs">{{ __mc("Autosaving every 20 seconds") }}
                - {{ __mc('Last saved at') }}
                @if ($this->lastSavedAt->isToday())
                    {{ $this->lastSavedAt->setTimezone(config('app.timezone'))->format('H:i:s') }}
                @else
                    {{ $this->lastSavedAt->toMailcoachFormat() }}
                @endif
            </p>
        @endif
    </div>

    @foreach ($contentItems as $index => $contentItem)
        <div wire:key="modals-{{ md5($preview[$contentItem->uuid]) }}">
            <div class="absolute" wire:key="preview-modal-{{ md5($preview[$contentItem->uuid]) }}">
                <x-mailcoach::preview-modal
                    id="preview-{{ $contentItem->uuid }}"
                    :html="$preview[$contentItem->uuid]"
                    :title="__mc('Preview') . ($contentItem->subject ? ' - ' . $contentItem->subject : '')"
                />
            </div>

            <x-mailcoach::modal :title="__mc('Send Test')" name="send-test-{{ $contentItem->uuid }}" :dismissable="true">
                <livewire:mailcoach::send-test :model="$contentItem"/>
            </x-mailcoach::modal>
        </div>
    @endforeach
</x-mailcoach::card>
