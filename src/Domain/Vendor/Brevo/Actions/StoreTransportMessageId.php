<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Actions;

use Illuminate\Mail\Events\MessageSent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class StoreTransportMessageId
{
    public function handle(MessageSent $event): void
    {
        if (! isset($event->data['send'])) {
            return;
        }

        if (! $event->message->getHeaders()->has('X-Brevo-Message-ID')) {
            return;
        }

        /** @var Send $send */
        $send = $event->data['send'];

        $transportMessageId = $event->message->getHeaders()->get('X-Brevo-Message-ID')->getBodyAsString();

        $transportMessageId = ltrim($transportMessageId, '<');
        $transportMessageId = rtrim($transportMessageId, '>');

        $send->storeTransportMessageId($transportMessageId);
    }
}
