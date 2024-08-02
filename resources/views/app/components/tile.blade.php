@props([
    'cols' => 1,
    'rows' => 1,
    'icon' => null,
    'title' => null,
    'link' => null,
    'linkLabel' => null,
    'danger' => null,
    'warning' => null,
    'success' => null,
    'target' => null,
    'wrapperClass' => '',
])
<!-- sm:col-span-1 sm:col-span-2 sm:col-span-3 sm:col-span-4 sm:col-span-5 sm:col-span-6 sm:col-span-7 sm:col-span-8 sm:col-span-9 sm:col-span-10 sm:col-span-11 sm:col-span-12 -->
<!-- md:col-span-1 md:col-span-2 md:col-span-3 md:col-span-4 md:col-span-5 md:col-span-6 md:col-span-7 md:col-span-8 md:col-span-9 md:col-span-10 md:col-span-11 md:col-span-12 -->
<!-- lg:col-span-1 lg:col-span-2 lg:col-span-3 lg:col-span-4 lg:col-span-5 lg:col-span-6 lg:col-span-7 lg:col-span-8 lg:col-span-9 lg:col-span-10 lg:col-span-11 lg:col-span-12 -->
<!-- xl:col-span-1 xl:col-span-2 xl:col-span-3 xl:col-span-4 xl:col-span-5 xl:col-span-6 xl:col-span-7 xl:col-span-8 xl:col-span-9 xl:col-span-10 xl:col-span-11 xl:col-span-12 -->
<!-- md:row-span-1 md:row-span-2 md:row-span-3 -->

<div
    class="
        min-h-[8rem] flex flex-col md:row-span-{{ $rows }} col-span-12 md:col-span-{{ round($cols * 2) }} xl:col-span-{{ $cols }}
        {{ $attributes->get('class') }}
        card pt-6 pb-4 px-8 rounded-lg
        {{ match(true) {
            $danger => 'bg-red-extra-light border border-red-light',
            $warning => 'bg-orange-extra-light border border-orange-light',
            $success => 'bg-green-extra-light border border-green-light',
            default => 'bg-white',
        } }}
    " {{ $attributes->except('class') }}>

    @if ($icon || $title)
        <header class="flex items-center gap-x-3 mb-5">
            @if ($icon)
                <div
                    class="
                        rounded-full w-5 h-5 flex-shrink-0 flex items-center justify-center
                        {{ match(true) {
                            $danger => 'bg-red',
                            $warning => 'bg-orange',
                            $success => 'bg-green',
                            default => 'bg-blue-dark',
                        } }}
                    "
                >
                    <x-icon :name="$icon" class="text-white w-3" />
                </div>
            @endif
            @if ($title)
                <h2 class="text-xl font-medium truncate">{{ $title }}</h2>
            @endif
        </header>
    @endif

    <div class="z-10 h-full w-full mt-auto text-sm {{ $wrapperClass }}">
        {{ $slot }}
    </div>

    @if ($link)
        <hr class="
            border-t my-4 -mx-8
            {{ match(true) {
                $danger => 'border-red-light',
                $warning => 'border-orange-light',
                $success => 'border-green-light',
                default => 'border-sand-bleak',
            } }}
        ">
        <a class="
            group font-medium text-sm flex items-center gap-x-2
            {{ match(true) {
                $danger => 'text-red-dark hover:text-red',
                $warning => 'text-orange-dark hover:text-orange',
                $success => 'text-green-dark hover:text-green',
                default => 'text-blue-dark hover:text-blue',
            } }}
        " href='{{$link}}' @if ($target) target="{{ $target }}" @else wire:navigate @endif>
            <span>
                {{ $linkLabel ?? __mc('Learn more') }}
            </span>
            <svg class="h-3 fill-current group-hover:translate-x-[0.25rem] transition-all" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 12"><path d="m12.957 6.602.6-.6-.6-.6L8.155.6l-.6-.6-1.197 1.2.6.6 3.354 3.354H0V6.85h10.312L6.96 10.203l-.6.6L7.554 12l.6-.6 4.802-4.798Z"/></svg>
        </a>
    @endif
</div>
