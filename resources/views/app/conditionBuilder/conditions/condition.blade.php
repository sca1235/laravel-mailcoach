<x-mailcoach::fieldset card class="p-0 gap-2 border border-snow rounded-md">
    <x-slot name="legend">
        <header class="flex items-center text-14 font-medium py-3.5 px-6 bg-sand-extra-light border-b border-snow rounded-t-md">
            {{ $title }}
        </header>
    </x-slot>
    <div class="flex items-center absolute top-2 right-2 gap-4 z-10">
        <x-mailcoach::confirm-button class="flex items-center gap-x-1 bg-transparent transition-colors hover:bg-white rounded-full p-2" :confirm-text="__mc('Are you sure you want to delete this action?')" on-confirm="() => $wire.delete({{ $index }})">
            <x-heroicon-s-trash class="w-3.5 text-red" />
        </x-mailcoach::confirm-button>
    </div>
    <div class="form-actions mt-0 w-full p-6">
        {{ $slot }}
    </div>
</x-mailcoach::fieldset>
