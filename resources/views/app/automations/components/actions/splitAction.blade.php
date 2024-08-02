<div>
    <x-mailcoach::fieldset card class="md:p-6 w-[10rem] mx-auto gap-y-4" :focus="$editing">
        <x-slot name="legend">
            <header class="flex flex-col items-center justify-center gap-2 text-base">
                @if ($action['class']::getIcon())
                    <div class="w-5 h-5 bg-blue-dark text-white rounded-full flex items-center justify-center">
                        <x-icon :name="$action['class']::getIcon()" class="w-3" />
                    </div>
                @endif
                <span class="font-normal whitespace-nowrap">
                    {{ $action['class']::getName() }}
                </span>
            </header>
        </x-slot>

        @if ($deletable)
            <dl class="-mb-6 -mx-6 px-6 py-2 flex items-center justify-center text-xs rounded-b-xl bg-white border-t border-sand-bleak">
                @if (count($leftActions) > 0 || count($rightActions) > 0)
                    <div x-data x-tooltip="'{{ __mc('Delete actions in branches first.') }}'">
                        <button class="opacity-75 flex items-center gap-x-1" type="button" disabled>
                            <x-heroicon-s-trash class="w-3.5" />
                            {{ __mc('Delete') }}
                        </button>
                    </div>
                @else
                    <x-mailcoach::confirm-button class="hover:text-red flex items-center gap-x-1" :confirm-text="__mc('Are you sure you want to delete this action?')" on-confirm="() => $wire.delete()">
                        <x-heroicon-s-trash class="w-3.5" />
                        {{ __mc('Delete') }}
                    </x-mailcoach::confirm-button>
                @endif
            </dl>
        @endif
    </x-mailcoach::fieldset>

    <div class="flex flex-col items-center">
        <div class="w-[2px] bg-sand h-8"></div>
    </div>

    <div class="flex">
        <div class="flex flex-grow flex-col items-center min-w-[34rem]">
            <div class="ml-auto w-1/2 bg-sand h-[2px]"></div>
            <div class="w-[2px] bg-sand h-2"></div>
            <div class="bg-sky-light text-navy-dark rounded-full px-6 py-3">{{ __mc('A') }}</div>
            <div class="flex flex-col" wire:ignore>
                <livewire:mailcoach::automation-builder name="{{ $uuid }}-left-actions" :automation="$automation" :actions="$leftActions" />
            </div>
            <div class="w-[2px] bg-sand flex-1"></div>
            <div class="ml-auto w-1/2 bg-sand h-[2px]"></div>
        </div>

        <div class="flex flex-grow flex-col items-center min-w-[34rem]">
            <div class="mr-auto w-1/2 bg-sand h-[2px]"></div>
            <div class="w-[2px] bg-sand h-2"></div>
            <div class="bg-sky-light text-navy-dark rounded-full px-6 py-3">{{ __mc('B') }}</div>
            <div class="flex flex-col" wire:ignore>
                <livewire:mailcoach::automation-builder name="{{ $uuid }}-right-actions" :automation="$automation" :actions="$rightActions" />
            </div>
            <div class="w-[2px] bg-sand flex-1"></div>
            <div class="mr-auto w-1/2 bg-sand h-[2px]"></div>
        </div>
    </div>
</div>

