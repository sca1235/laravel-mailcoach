<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Content\Models\Click;

class AutomationMailLinkClickedEvent
{
    public function __construct(
        public Click $automationMailClick,
    ) {
    }
}
