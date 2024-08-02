<?php

namespace Spatie\Mailcoach\Livewire\Automations\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\Mailcoach\Livewire\Automations\AutomationTriggerComponent;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public array $date = [
        'date' => null,
        'hours' => null,
        'minutes' => null,
    ];

    public string $repeat = '';

    public function mount()
    {
        $default = now()->setTimezone(config('mailcoach.timezone')
            ?? config('app.timezone'))->addHour()->startOfHour();

        $this->date['date'] ??= $default->format('Y-m-d');
        $this->date['hours'] ??= (int) $default->format('H');
        $this->date['minutes'] ??= (int) $default->format('i');

        if (! $trigger = $this->automation->getTrigger()) {
            return;
        }

        if (! $trigger instanceof DateTrigger) {
            return;
        }

        $this->date['date'] = $trigger->date->format('Y-m-d');
        $this->date['hours'] = (int) $trigger->date->format('H');
        $this->date['minutes'] = (int) $trigger->date->format('i');
        $this->repeat = $trigger->repeat ?? '';
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.dateTrigger');
    }
}
