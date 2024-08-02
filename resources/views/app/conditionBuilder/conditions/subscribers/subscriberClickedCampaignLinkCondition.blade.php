<x-mailcoach::condition :index="$index" :title="$title">
    <div wire:key="campaign-{{ $campaignId }}-options" class="col-span-12 grid grid-cols-12 gap-4 w-full">
        <div class="col-span-4">
            <x-mailcoach::select-field
                :label="__mc('Campaign')"
                name="campaign-{{ $index }}"
                :options="$campaigns"
                :sort="false"
                wire:model.live="storedCondition.value.campaignId"
                required
            />
        </div>
        <div class="col-span-4">
            <x-mailcoach::select-field
                :label="__mc('Comparison')"
                name="operator-{{ $index }}"
                wire:model.live="storedCondition.comparison_operator"
                :options="$storedCondition['condition']['comparison_operators'] ?? []"
                :sort="false"
                required
            />
        </div>
        @if(!in_array($storedCondition['comparison_operator'], ['any', 'none']))
            <div class="col-span-4">
                <x-mailcoach::select-field
                    :label="__mc('Value')"
                    name="value-{{ $index }}"
                    :options="$options"
                    wire:model.live="storedCondition.value.link"
                    required
                />
            </div>
        @endif
    </div>
</x-mailcoach::condition>
