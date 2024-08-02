<div class="grid items-start justify-start gap-x-16 gap-y-8 md:grid-cols-[auto,auto]">
    @foreach (collect($availableConditions)->groupBy('category') as $category => $conditions)
        <div>
            <h4 class="font-medium mb-3 px-3">
                {{ \Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory::from($category)->label() }}
            </h4>
            <ul>
                @foreach ($conditions as $condition)
                    <li>
                        <a class="block bg-white text-navy transition-colors hover:bg-sand-extra-light px-3 py-2 whitespace-nowrap" href="#" wire:click.prevent="add('{{ addslashes($condition['value']) }}')">
                            {{ $condition['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
