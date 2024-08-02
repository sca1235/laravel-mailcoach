<?php

namespace Spatie\Mailcoach\Domain\Shared\Listeners;

use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;

class SetWebhookCallProcessedAt
{
    public function handle(WebhookCallProcessedEvent $event)
    {
        $event->webhookCall->update([
            'processed_at' => now(),
        ]);
    }
}
