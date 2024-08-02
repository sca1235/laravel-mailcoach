<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberOpenedCampaignConditionComponent extends ConditionComponent
{
    public EmailList $emailList;

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->options = self::getCampaignClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->has('contentItem.opens')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => __mc('Opened Any'),
                'none' => __mc('Did Not Open Any'),
                'equals' => __mc('Opened'),
                'not-equals' => __mc('Did Not Open'),
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function getValue(): mixed
    {
        $value = parent::getValue();

        if (empty($value)) {
            return null;
        }

        return $value;
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberOpenedCampaignCondition');
    }
}
