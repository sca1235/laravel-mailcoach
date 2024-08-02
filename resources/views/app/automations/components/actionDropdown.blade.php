<div class="flex flex-col items-center gap-y-1 hover:gap-y-2 group">
    <div class="w-[2px] bg-sand h-4 group-hover:h-3"></div>
    <div class="flex justify-center">
        <x-mailcoach::dropdown>
            <x-slot name="trigger">
                <div class="group button button-rounded bg-sky hover:bg-blue-dark scale-75 group-focus:scale-100 group-hover:scale-100 transition-all duration-300 shadow-dropdown" title="{{__mc('Insert action')}}">
                    <x-heroicon-s-plus class="w-3.5 scale-90 group-focus:scale-100 group-hover:scale-100" />
                </div>
            </x-slot>

            <div class="p-6">
                @include('mailcoach::app.automations.components.actionCategories')
            </div>
        </x-mailcoach::dropdown>
    </div>
    <div class="w-[2px] bg-sand h-4 group-hover:h-3"></div>
</div>
