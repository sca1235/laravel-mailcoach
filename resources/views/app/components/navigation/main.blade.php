@php use Spatie\Mailcoach\MainNavigation; @endphp
<header class="w-full border-b border-sand p-3.5 lg:p-8 text-sm relative">
    <nav class="max-w-layout mx-auto">
        <div class="flex items-center gap-x-4 lg:gap-x-10 gap-y-6">
            <div class="flex items-center">
                <a wire:navigate href="{{ route(config('mailcoach.redirect_home')) }}" class="flex items-center group">
                    <span class="flex h-8 w-8 lg:h-12 lg:w-12 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </a>
            </div>

            <!-- Desktop nav -->
            <div class="hidden sm:flex flex-row items-center gap-y-8 gap-x-6 lg:gap-x-8 px-8 h-8 lg:h-12 bg-white text-xs lg:text-sm rounded-full">
                @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

                @foreach (app(MainNavigation::class)->tree() as $index => $item)
                    <div class="navigation-dropdown-trigger group">
                        <a wire:navigate class="font-medium hover:text-blue-dark {{ $item['active'] ? 'text-blue-dark' : '' }}" href="{{ $item['url'] }}">
                            {{ $item['title'] }}
                        </a>
                    </div>
                @endforeach

                @include('mailcoach::app.layouts.partials.afterLastMenuItem')
            </div>

            <!-- Mobile nav -->
            <div class="sm:hidden ml-auto static" x-data="{ open: false }">
                <a x-on:click.prevent="open = !open" href="" class="flex items-center gap-x-2 group text-white">
                    <button class="rounded-full h-8 px-4 text-sm font-medium transition-colors bg-navy group-hover:bg-navy-dark">
                        Menu
                    </button>
                </a>

                <div class="absolute right-0 bg-white rounded-xl p-4.5 mt-2 font-medium text-navy w-full max-w-sm z-50" x-show="open" x-on:click.outside="open = false" x-transition x-cloak>
                    <div class="flex flex-col gap-4">
                        @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

                        @foreach (app(MainNavigation::class)->tree() as $index => $item)
                            <div class="navigation-dropdown-trigger group">
                                <a wire:navigate class="font-medium hover:text-blue-dark {{ $item['active'] ? 'text-blue-dark' : '' }}" href="{{ $item['url'] }}">
                                    {{ $item['title'] }}
                                </a>
                            </div>
                        @endforeach

                        @include('mailcoach::app.layouts.partials.afterLastMenuItem')
                    </div>
                </div>
            </div>


            <div class="sm:ml-auto flex-shrink-0">
                @include('mailcoach::app.layouts.partials.headerRight')
            </div>
        </div>
    </nav>
</header>
