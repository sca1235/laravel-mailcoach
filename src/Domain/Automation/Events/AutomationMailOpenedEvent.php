<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;

class AutomationMailOpenedEvent
{
    public function __construct(
        public Open $automationMailOpen,
    ) {
    }
}
