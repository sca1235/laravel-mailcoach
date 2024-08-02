@props([
    'id' => 'preview',
    'html' => '',
    'title' => 'Preview',
])
<x-filament::modal id="{{ $id }}" slide-over width="3xl">
    <x-slot:heading>
        <p class="text-navy font-medium text-lg mb-3">{{ $title }}</p>
        <x-mailcoach::alert type="info" class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::alert>
    </x-slot:heading>
    <x-mailcoach::web-view :id="$id" :html="$html"></x-mailcoach::web-view>
</x-filament::modal>
