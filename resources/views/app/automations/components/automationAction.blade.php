<x-mailcoach::fieldset card class="md:p-6 min-w-[32rem] gap-y-4" :focus="$editing" wire:init="loadData">
    <x-slot name="legend">
        <header class="flex flex-col items-center justify-center gap-2 text-base">
            @if ($action['class']::getIcon())
                <div class="w-5 h-5 bg-blue-dark text-white rounded-full flex items-center justify-center">
                    <x-icon :name="$action['class']::getIcon()" class="w-3" />
                </div>
            @endif
            <span class="font-normal">
                <div class="text-center font-medium">
                    {{ $legend ?? $action['class']::getName() }}
                </div>
            </span>
        </header>
    </x-slot>

    @if ($editing)
        <hr class="border-t border-sand-bleak -mx-4 md:-mx-6">
        <div class="form-actions">
            {{ $form ?? '' }}
        </div>
    @else
        @if(! empty(trim($content ?? '')))
            <hr class="border-t border-sand-bleak -mx-4 md:-mx-6">
            <div class="">
                {{ $content }}
            </div>
        @endif
    @endif

    <dl class="-mb-6 -mx-4 md:-mx-6 px-6 py-2 flex items-center justify-between text-xs rounded-b-xl bg-white border-t border-sand-bleak">
        <div class="flex items-center gap-4">
            @if ($editing)
                <x-mailcoach::button type="button" wire:key="save-{{ $index }}" wire:click="save" class="text-xs py-1 px-3 h-6">
                    <x-slot:icon>
                        <x-heroicon-s-check class="w-3.5" />
                    </x-slot:icon>
                    {{ __mc('Save') }}
                </x-mailcoach::button>
            @elseif ($editable)
                <button type="button" class="hover:text-blue-dark flex items-center gap-x-1" wire:key="edit-{{ $index }}" wire:click="edit">
                    <x-heroicon-s-pencil-square class="w-3.5" />
                    {{ __mc('Edit') }}
                </button>
            @endif
            @if ($deletable)
                <x-mailcoach::confirm-button class="hover:text-red flex items-center gap-x-1" :confirm-text="__mc('Are you sure you want to delete this action?')" on-confirm="() => $wire.delete()">
                    <x-heroicon-s-trash class="w-3.5" />
                    {{ __mc('Delete') }}
                </x-mailcoach::confirm-button>
            @endif
        </div>

        <div class="flex items-center gap-x-3">
            <span>
                Active
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['active']) ? number_format($action['active']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
            <div class="w-px bg-sand-bleak h-4"></div>
            <span>
                Completed
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['completed']) ? number_format($action['completed']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
            <div class="w-px bg-sand-bleak h-4"></div>
            <span>
                Halted
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['halted']) ? number_format($action['halted']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
        </div>
    </dl>
</x-mailcoach::fieldset>
