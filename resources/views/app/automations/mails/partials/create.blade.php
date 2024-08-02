<form class="form-grid"
      wire:submit="saveAutomationMail"
      @keydown.prevent.window.cmd.s="$wire.call('saveAutomationMail')"
      @keydown.prevent.window.ctrl.s="$wire.call('saveAutomationMail')"
      method="POST"
>
    @csrf

    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__mc('Email name')"
        required
    />

    @if(count($templateOptions) > 1)
        <x-mailcoach::select-field
            :label="__mc('Template')"
            :options="$templateOptions"
            wire:model.lazy="template_id"
            name="template_id"
        />
    @endif

    <div class="flex items-center gap-x-3">
        <x-mailcoach::button :label="__mc('Create email')" />
        <x-mailcoach::button-tertiary :label="__mc('Cancel')" x-on:click="$dispatch('close-modal', { id: 'create-automation-mail' })" />
    </div>
</form>
