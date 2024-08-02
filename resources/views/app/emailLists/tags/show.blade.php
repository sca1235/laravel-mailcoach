<form
    class="card-grid"
    method="POST"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model="name" required />

    <x-mailcoach::alert type="help">
        <p>{{ __mc('Whether the subscriber can choose to add or remove this tag on "Manage your preferences" page.') }}</p>
        <p>{!! __mc('This page can be linked to by using the <code>::preferencesUrl::</code> placeholder in your emails.') !!}</p>
        <p>{!! __mc('You can view an example of your page with all the currently enabled tags <a href=":url">here</a>', [
            'url' => route('mailcoach.manage-preferences', ['example', $tag->emailList->uuid]),
        ]) !!}</p>
    </x-mailcoach::alert>
    <x-mailcoach::checkbox-field :label="__mc('Visible on manage preferences page')" name="visible_in_preferences" wire:model.live="visible_in_preferences" />

    @if ($visible_in_preferences)
        <x-mailcoach::textarea-field
            :label="__mc('Description')"
            name="description"
            wire:model="description"
        />
        <x-mailcoach::alert type="info" class="-mt-4">{{ __mc('You can give the tag a description which is shown on the page.') }}</x-mailcoach::alert>
    @endif

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Save tag')" />
    </x-mailcoach::form-buttons>
</x-mailcoach::card>

<x-mailcoach::api-card
    resource-name="tag uuid"
    resource="tag"
    :uuid="$tag->uuid"
/>
</form>

