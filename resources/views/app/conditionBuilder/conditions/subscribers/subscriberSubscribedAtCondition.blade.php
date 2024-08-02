<x-mailcoach::condition :index="$index" :title="$title">
    @if($storedCondition['comparison_operator'] === 'between')
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

        <div class="col-span-4">
            <x-mailcoach::date-field
                :label="__mc('First')"
                name="value-{{ $index }}0"
                wire:model.live.debounce.250ms="storedCondition.value.0"
                minDate=null
                maxDate="today"
                required
            />
        </div>

        <div class="col-span-4">
            <x-mailcoach::date-field
                :label="__mc('last')"
                name="value-{{ $index }}1"
                wire:model.live.debounce.250ms="storedCondition.value.1"
                minDate=null
                maxDate="null"
                required
            />
        </div>
    @else
        <div class="col-span-6">
            <x-mailcoach::select-field
                :label="__mc('Comparison')"
                name="operator-{{ $index }}"
                wire:model.live.debounce.250ms="storedCondition.comparison_operator"
                :options="$storedCondition['condition']['comparison_operators'] ?? []"
                :sort="false"
                required
            />
        </div>

        <div class="col-span-6">
            <x-mailcoach::date-field
                :label="__mc('Value')"
                name="value-{{ $index }}"
                wire:model.live.debounce.250ms="storedCondition.value"
                minDate=null
                maxDate=null
                required
            />
        </div>
    @endif
</x-mailcoach::condition>
