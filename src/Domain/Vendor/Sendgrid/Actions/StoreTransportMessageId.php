<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions;

use Illuminate\Mail\Events\MessageSent;

class StoreTransportMessageId
{
    public function handle(MessageSent $event): void
    {
        if (! isset($event->data['send'])) {
            return;
        }

        if (! $messageId = $event->sent->getMessageId()) {
            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = $event->data['send'];

        $messageId = ltrim($messageId, '<');
        $messageId = rtrim($messageId, '>');

        $send->storeTransportMessageId($messageId);
    }
}
