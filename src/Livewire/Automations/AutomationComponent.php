<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public string $bg = 'sand';

    abstract public function render();
}
