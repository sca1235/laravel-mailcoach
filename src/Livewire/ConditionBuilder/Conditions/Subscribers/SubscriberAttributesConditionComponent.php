<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberAttributesConditionComponent extends ConditionComponent
{
    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->options = Subscriber::attributesFields();
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberAttributesCondition');
    }
}
