<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberEmailQueryConditionComponent extends ConditionComponent
{
    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberEmailCondition');
    }
}
