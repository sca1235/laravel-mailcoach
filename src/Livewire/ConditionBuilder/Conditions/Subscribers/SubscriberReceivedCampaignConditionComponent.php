<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberReceivedCampaignConditionComponent extends ConditionComponent
{
    public EmailList $emailList;

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->options = self::getCampaignClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => __mc('Received any'),
                'none' => __mc('Did not receive any'),
                'equals' => __mc('Received'),
                'not-equals' => __mc('Did not receive'),
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberReceivedCampaignCondition');
    }
}
