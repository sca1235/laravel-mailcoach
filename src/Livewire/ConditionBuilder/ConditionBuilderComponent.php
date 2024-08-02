<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\ConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

class ConditionBuilderComponent extends Component
{
    public EmailList $emailList;

    /** @var array<StoredCondition> */
    public array $storedConditions;

    /** @var array<Condition> */
    public array $availableConditions;

    protected $listeners = [
        'storedConditionUpdated' => 'updateStoredCondition',
        'storedConditionDeleted' => 'deleteStoredCondition',
    ];

    public function mount(array $storedConditions = []): void
    {
        $this->storedConditions = $storedConditions;
        $this->availableConditions = ConditionCollection::allConditions()->options();
    }

    public function updateStoredCondition($index, $data): void
    {
        $this->storedConditions[$index] = $data;

        $this->dispatch('storedConditionsUpdated', $this->storedConditions);
    }

    public function deleteStoredCondition($index): void
    {
        unset($this->storedConditions[$index]);
        $this->storedConditions = array_values($this->storedConditions);

        $this->dispatch('storedConditionsUpdated', $this->storedConditions);
    }

    public function add(string $key): void
    {
        $condition = app(CreateConditionFromKeyAction::class)->execute($key);

        $this->storedConditions[] = StoredCondition::blueprint($condition);

        $this->dispatch('storedConditionsUpdated', $this->storedConditions);
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditionBuilder');
    }
}
