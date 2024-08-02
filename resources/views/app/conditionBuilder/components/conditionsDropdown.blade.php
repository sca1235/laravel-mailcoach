<div class="flex {{ count($storedConditions) ? 'justify-center -my-1.5' : 'justify-start mt-1' }}">
    <x-mailcoach::dropdown direction="right">
        <x-slot name="trigger">
            <div class="button button-tertiary">
                <x-heroicon-s-plus-circle class="w-4 text-navy" />
                {{ __mc('Add condition') }}
            </div>
        </x-slot>

        <div class="p-6">
            @include('mailcoach::app.conditionBuilder.components.conditionCategories')
        </div>
    </x-mailcoach::dropdown>
</div>
