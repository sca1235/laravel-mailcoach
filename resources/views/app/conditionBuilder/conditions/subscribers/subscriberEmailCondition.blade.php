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
        <x-mailcoach::text-field
            :label="__mc('Value')"
            name="value-{{ $index }}"
            wire:model.live.debounce.250ms="storedCondition.value"
            required
        />
    </div>
</x-mailcoach::condition>
