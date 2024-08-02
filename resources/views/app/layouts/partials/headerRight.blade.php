<div
    x-data="{ open: false }"
    x-on:click.outside="open = false"
>
    <div class="inline-flex items-center h-12 gap-x-3 cursor-pointer" x-on:click="open = true" x-ref="trigger">
        <img class="flex-shrink-0 w-8 lg:w-10 h-8 lg:h-10 border-2 rounded-full border-white shadow-card" src="https://www.gravatar.com/avatar/{{ md5(auth()->guard(config('mailcoach.guard'))->user()->email) }}?d=mp" alt="{{ auth()->guard(config('mailcoach.guard'))->user()->name }}">
    </div>
    <div class="absolute z-20 w-[240px] ml-4" x-cloak x-anchor.bottom-end.offset.16="$refs.trigger" x-show="open" x-transition>
        <svg x-bind:style="`right: ${$refs.trigger.getBoundingClientRect().width - 15}px`" class="w-[24px] absolute top-0 mt-px -translate-y-1/2 z-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 22 26"><path fill="#fff" d="M2.25 13.665c-.83 0-1.246-1.146-.659-1.816l8.357-9.537a.853.853 0 0 1 1.318 0l8.356 9.537c.587.67.172 1.816-.659 1.816H2.25Z"/><path fill="#E9E9E9" fill-rule="evenodd" d="M2.25 14.197c-1.245 0-1.87-1.719-.989-2.724l8.357-9.537c.546-.624 1.431-.624 1.977 0l8.357 9.537c.88 1.005.257 2.724-.989 2.724H2.25Zm-.33-1.972c-.293.335-.085.908.33.908h16.713c.415 0 .623-.573.33-.908l-8.357-9.537a.426.426 0 0 0-.659 0L1.92 12.225Z" clip-rule="evenodd"/><path fill="#fff" d="M1.609 14.819c-.823-.016-1.225-1.152-.643-1.816l8.982-10.25a.853.853 0 0 1 1.318 0l9.297 10.61c.593.677.163 1.833-.675 1.816L10.803 15l-9.194-.181Z"/></svg>
        <div class="shadow-profile rounded-xl">
            <div class="bg-white px-4.5 py-6 rounded-xl flex flex-col gap-4.5 border border-snow">
                <div class="flex flex-col gap-1.5">
                    <span class="text-navy font-semibold text-base">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                    <span class="text-navy-bleak-light truncate text-sm">{{ str_replace(['https://', 'http://'], '', config('app.url')) }}</span>
                </div>
                <div class="flex flex-col gap-y-3">
                    @foreach (\Spatie\Mailcoach\Mailcoach::$userMenuItems['before'] as $item)
                        @include('mailcoach::app.layouts.partials.menuItem')
                    @endforeach
                    @can ('viewMailcoach')
                        @include('mailcoach::app.layouts.partials.menuItem', [
                            'item' => (object) [
                                'isForm' => false,
                                'isDivider' => false,
                                'isExternal' => false,
                                'label' => __mc('Configuration'),
                                'icon' => 'heroicon-s-cog-8-tooth',
                                'url' => route('general-settings'),
                            ]
                        ])
                    @endcan
                    @foreach (\Spatie\Mailcoach\Mailcoach::$userMenuItems['after'] as $item)
                        @include('mailcoach::app.layouts.partials.menuItem')
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
