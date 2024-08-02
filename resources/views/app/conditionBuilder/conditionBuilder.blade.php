<div class="grid gap-9">
    @foreach ($storedConditions as $index => $storedCondition)
        <div>
            @livewire(app(\Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction::class)->execute($storedCondition['condition']['key'])->getComponent(), [
                'index' => $index,
                'storedCondition' => $storedCondition,
                'emailList' => $emailList,
            ], key('stored-condition-' . $storedCondition['condition']['key'] . '-' . $index))
            @unless($loop->last)
                <div class="text-center uppercase font-bold tracking-wider text-xs mt-4 -mb-5">{{ __mc('And') }}</div>
            @endunless
        </div>
    @endforeach

    @include('mailcoach::app.conditionBuilder.components.conditionsDropdown')

    <hr class="border-t border-sand-bleak" />
</div>
