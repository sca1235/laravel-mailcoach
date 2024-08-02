<form
    class="form-grid"
    wire:submit="saveTemplate"
    @keydown.prevent.window.cmd.s="$wire.call('saveTemplate')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveTemplate')"
    method="POST"
>
    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__mc('Transactional email')"
        required
    />

    <?php
        $editor = config('mailcoach.template_editor', \Spatie\Mailcoach\Domain\Shared\Support\Editor\TextEditor::class);
    $editorName = (new ReflectionClass($editor))->getShortName();
    ?>
    <x-mailcoach::select-field
        :label="__mc('Type')"
        name="type"
        wire:model.lazy="type"
        :options="[
            'html' => 'HTML (' . $editorName . ')',
            'markdown' => 'Markdown',
            'blade' => 'Blade',
            'blade-markdown' => 'Blade with Markdown',
        ]"
    />

    @if ($type === 'html' && count($templateOptions) > 1)
        <x-mailcoach::select-field
            :label="__mc('Template')"
            :options="$templateOptions"
            wire:model.lazy="template_id"
            position="top"
            name="template_id"
        />
    @endif

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create email')" />
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-transactional-template' })" />
    </div>
</form>
