@if ((isset($header) && $header) || (isset($title) && $title))
    <div class="flex-none flex items-center justify-between w-full mb-6 md:mb-8">
        <div class="flex items-center w-full h-12">
            @if (isset($header) && $header)
                {{ $header }}
            @elseif (isset($title) && $title)
                <div class="flex items-center gap-x-6">
                    @if (isset($originTitle) && $originTitle !== $title)
                        <a wire:navigate x-data x-tooltip="'{{ __mc('Back to ' . $originTitle) }}'" href="{{ $originHref ?? '' }}">
                            <svg class="w-5 h-5 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30"><g clip-path="url(#clip0_386_1588)"><path fill="#fff" d="M30 15a15 15 0 1 0-30 0 15 15 0 0 0 30 0Z"/><path fill="#C2C0BC" d="M6.973 15.996a1.4 1.4 0 0 1 0-1.986l6.562-6.569a1.406 1.406 0 0 1 1.986 1.986l-4.16 4.16 10.67.007c.78 0 1.407.627 1.407 1.406 0 .78-.627 1.406-1.407 1.406h-10.67l4.16 4.16a1.406 1.406 0 0 1-1.986 1.986l-6.562-6.556Z"/></g><defs><clipPath id="clip0_386_1588"><path fill="#fff" d="M0 0h30v30H0z"/></clipPath></defs></svg>
                        </a>
                    @endif
                    <h1 class="markup-h1 font-title leading-none">{{ $title }}</h1>
                </div>
            @endif
        </div>

        @if (($create ?? false) || ($createComponent ?? false))
            <div class="buttons flex">
                <x-mailcoach::button
                    x-on:click="$dispatch('open-modal', { id: 'create-{{ $create }}' })"
                    :label="$createText ?? __mc('New ' . $create)"
                    class="mb-0"
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path fill="#fff" d="M15.11 8A7.112 7.112 0 1 1 .889 8 7.112 7.112 0 0 1 15.11 8Zm-3.737-.667H8.666V4.627a.667.667 0 0 0-1.333 0v2.706H4.626a.667.667 0 0 0 0 1.334h2.707v2.706a.667.667 0 0 0 1.333 0V8.667h2.707a.667.667 0 1 0 0-1.334Z"/></svg>
                    </x-slot:icon>
                </x-mailcoach::button>

                <x-mailcoach::modal :title="$createText ?? __mc('Create ' . $create)"
                                    name="create-{{ $create }}">
                    @if ($createComponent ?? '')
                        @livewire($createComponent, $createData ?? [])
                    @else
                        @livewire('mailcoach::create-' . $create, $createData ?? [])
                    @endif
                </x-mailcoach::modal>

                {{ $afterCreate ?? '' }}
            </div>
        @endif
    </div>
@endif
