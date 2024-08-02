<x-mailcoach::condition :index="$index" :title="$title">
    <div class="col-span-4">
        <x-mailcoach::select-field
            :label="__mc('Comparison')"
            name="operator-{{ $index }}"
            wire:model="storedCondition.comparison_operator"
            :options="$storedCondition['condition']['comparison_operators'] ?? []"
            :sort="false"
            required
        />
    </div>
    @if(!in_array($storedCondition['comparison_operator'], ['any', 'none', null]))
        <div class="col-span-4">
            <x-mailcoach::select-field
                :label="__mc('Value')"
                name="value-{{ $index }}"
                :options="$options"
                wire:model.live.debounce.250ms="storedCondition.value"
                required
            />
        </div>
    @endif
</x-mailcoach::condition>
