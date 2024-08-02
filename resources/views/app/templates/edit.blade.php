<div class="card-grid">
    <x-mailcoach::card>
        <x-mailcoach::alert type="help" full>
            <p>{{ __mc('A template is a reusable layout that can be used as a starting point for your campaigns, automation emails or transactional mails.') }}</p>
            <p>{!! __mc('Create slots in your template by adding the name in triple brackets, for example: <code>[[[content]]]</code>. You can add as many slots as you like.') !!}</p>
            <span>{!! __mc('By default your chosen editor will be loaded, you can append <code>:text</code> to your placeholder for a simple text input, or <code>:image</code> for an image upload that will fill the uploaded URL in the slot, for example: <code>[[[preheader:text]]]</code> or <code>[[[logo:image]]]</code>') !!}</span>
        </x-mailcoach::alert>

        <form
            class="form-grid mt-6"
            wire:submit="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
            <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model="name" required />

            @livewire(config('mailcoach.template_editor'), [
                'model' => $template,
                'quiet' => true,
            ])

            <x-mailcoach::form-buttons>
                <x-mailcoach::replacer-help-texts :model="$template" />

                <x-mailcoach::form-buttons>
                    <div class="flex gap-x-2">
                        <x-mailcoach::button
                            wire:click.prevent="save"
                            :label="__mc('Save template')"
                        />

                        @if (config('mailcoach.template_editor') !== \Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class)
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
                    </div>

                    @if (! preg_match_all('/\[\[\[(.*?)\]\]\]/', $html, $matches) && ! preg_match_all('/\[\[\[(.*?)\]\]\]/', $template->html, $matches))
                        <x-mailcoach::alert type="info" class="mt-6">
                            {!! __mc('We found no slots in this template. You can add slots by adding the name in triple brackets, for example: <code>[[[content]]]</code>.') !!}
                        </x-mailcoach::alert>
                    @endif
                </x-mailcoach::form-buttons>

            </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>

    <x-mailcoach::api-card
        resource-name="template uuid"
        resource="template"
        :uuid="$template->uuid"
    />
</div>
