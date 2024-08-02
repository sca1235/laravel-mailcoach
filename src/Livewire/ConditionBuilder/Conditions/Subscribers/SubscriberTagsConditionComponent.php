<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberTagsConditionComponent extends ConditionComponent
{
    public EmailList $emailList;

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->options = self::getTagClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->orderBy('type')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberTagsCondition');
    }
}
