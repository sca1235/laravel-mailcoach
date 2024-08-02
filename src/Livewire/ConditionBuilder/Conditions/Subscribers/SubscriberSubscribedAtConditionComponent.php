<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberSubscribedAtConditionComponent extends ConditionComponent
{
    public function updatedStoredCondition($newValue, $property)
    {
        if ($property === 'comparison_operator' && $newValue === ComparisonOperator::Between->value) {
            $this->storedCondition['value'] = [];
        }

        if ($property === 'comparison_operator' && $newValue !== ComparisonOperator::Between->value) {
            $this->storedCondition['value'] = '';
        }
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberSubscribedAtCondition');
    }
}
