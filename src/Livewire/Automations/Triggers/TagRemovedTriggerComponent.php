<?php

namespace Spatie\Mailcoach\Livewire\Automations\Triggers;

use Spatie\Mailcoach\Livewire\Automations\AutomationTriggerComponent;

class TagRemovedTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.tagRemovedTrigger');
    }
}
