<form action="" class="grid grid-cols-1 gap-6" wire:submit="sendTest">
    @php($automationMailClass = \Spatie\Mailcoach\Mailcoach::getAutomationMailClass())
    @if ($model instanceof $automationMailClass)
        <x-mailcoach::text-field
            :label="__mc('From address')"
            name="from_email"
            :required="true"
            type="text"
            wire:model="from_email"
        />
    @endif
    <x-mailcoach::text-field
        :label="__mc('Test addresses')"
        :placeholder="__mc('Email(s) comma separated')"
        name="emails"
        :required="true"
        type="text"
        wire:model="emails"
    />
    <x-mailcoach::button :label="__mc('Send test')"/>
</form>
