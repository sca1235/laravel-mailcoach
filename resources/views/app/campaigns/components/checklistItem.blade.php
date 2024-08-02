@props([
    'warning' => false,
    'neutral' => false,
    'icon' => null,
    'test' => true,
    'border' => true,
    'value' => '',
    'label' => '',
])
<tr class="flex flex-col max-w-full overflow-x-auto mb-8 sm:table-row">
    <td class="block sm:table-cell sm:py-6 sm:pr-6 sm:border-b align-top {{ $border ? 'border-snow' : 'border-transparent' }}">
        <x-mailcoach::health-label
            reverse
            :warning="$warning"
            :neutral="$neutral"
            :icon="$icon"
            :test="$test"
        >
            <x-slot:label>
                <div class="flex items-center">
                    {{ $label }}
                    @isset($editLink)
                        <a href="{{ $editLink }}" class="link ml-2">
                            <x-heroicon-s-pencil-square class="w-4" />
                        </a>
                    @endisset
                </div>
            </x-slot:label>
        </x-mailcoach::health-label>
    </td>
    <td class="block sm:table-cell break-all markup markup-code markup-links py-6 border-b {{ $border ? 'border-snow' : 'border-transparent' }}">
        {{ $slot }}
        <span>{{ $value }}</span>
    </td>
</tr>
