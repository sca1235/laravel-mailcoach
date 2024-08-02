@props([
    'name',
    'push' => true,
    'title' => null,
    'description' => null,
    'icon' => null,
    'iconColor' => null,
    'confirmText' => __mc('Confirm'),
    'cancelText' =>  __mc('Cancel'),
    'dismissable' => false,
    'slideOver' => false,
    'footerActions' => [],
    'alignment' => \Filament\Support\Enums\Alignment::Left,
])
@if ($push)
    @push('modals')
        <x-filament::modal
            id="{{ $name }}"
            :alignment="$alignment"
            :slide-over="$slideOver"
            :close-by-clicking-away="$dismissable"
            :close-button="true"
            x-init="window.location.hash === '#{{ $name }}' ? $dispatch('open-modal', { id: '{{ $name }}' }) : null"
            x-on:close-modal.window="history.replaceState(null, null, ' ')"
            x-on:open-modal.window="(event) => window.location.hash = event.detail.id"
            :heading="$title"
            :description="$description"
            :icon="$icon"
            :icon-color="$iconColor"
            {{ $attributes }}
        >
            <x-slot:footer>
                {{ $slot }}
            </x-slot:footer>
        </x-filament::modal>
    @endpush
@else
    <x-filament::modal
        id="{{ $name }}"
        :alignment="$alignment"
        :slide-over="$slideOver"
        :close-by-clicking-away="$dismissable"
        :close-button="true"
        x-init="window.location.hash === '#{{ $name }}' ? $dispatch('open-modal', { id: '{{ $name }}' }) : null"
        x-on:close-modal.window="history.replaceState(null, null, ' ')"
        x-on:open-modal.window="(event) => window.location.hash = event.detail.id"
        :heading="$title"
        :description="$description"
        :icon="$icon"
        :icon-color="$iconColor"
        {{ $attributes }}
    >
        <x-slot:footer>
            {{ $slot }}
        </x-slot:footer>
    </x-filament::modal>
@endif
