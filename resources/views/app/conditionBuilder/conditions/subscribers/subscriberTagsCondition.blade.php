<x-mailcoach::condition :index="$index" :title="$title">
    <div class="col-span-6">
        <x-mailcoach::select-field
            :label="__mc('Comparison')"
            name="operator-{{ $index }}"
            wire:model="storedCondition.comparison_operator"
            :options="$storedCondition['condition']['comparison_operators'] ?? []"
            :sort="false"
            required
        />
    </div>
    <div class="col-span-6">
        <x-mailcoach::select-field
            :label="__mc('Value')"
            name="value-{{ $index }}"
            :options="$options"
            :sort="false"
            multiple
            wire:model.live.debounce.250ms="storedCondition.value"
            required
        />
    </div>
</x-mailcoach::condition>
