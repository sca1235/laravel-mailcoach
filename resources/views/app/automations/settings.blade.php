<form
    class="card-grid"
    wire:submit="save(new URLSearchParams(new FormData($event.target)).toString())"
    method="POST"
    novalidate
>
    <x-mailcoach::card>
        <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model.lazy="name" required />

        <div class="form-field gap-y-4 flex flex-col">
            <label class="label" for="repeat_enabled">
                {{ __mc('Repeat') }}
            </label>

            <x-mailcoach::checkbox-field
                :label="__mc('Allow for subscribers to go through the automation more than once')"
                name="repeat_enabled"
                wire:model.lazy="repeat_enabled"
            />

            @if ($repeat_enabled)
                <x-mailcoach::checkbox-field
                    :label="__mc('Repeat only when subscriber was halted')"
                    name="repeat_only_after_halt"
                    wire:model.lazy="repeat_only_after_halt"
                />
            @endif
        </div>
    </x-mailcoach::card>
    @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation])

    <x-mailcoach::api-card
        resource-name="automation uuid"
        resource="automation"
        :uuid="$automation->uuid"
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
