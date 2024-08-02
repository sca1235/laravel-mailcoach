<?php

namespace Spatie\Mailcoach\Domain\Shared\Events;

use Spatie\WebhookClient\Models\WebhookCall;

class WebhookCallProcessedEvent
{
    public function __construct(
        public WebhookCall $webhookCall
    ) {
    }
}
