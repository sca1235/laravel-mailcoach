<?php

namespace Spatie\Mailcoach\Livewire\Automations\Triggers;

use Spatie\Mailcoach\Livewire\Automations\AutomationTriggerComponent;

class NoTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.noTrigger');
    }
}
