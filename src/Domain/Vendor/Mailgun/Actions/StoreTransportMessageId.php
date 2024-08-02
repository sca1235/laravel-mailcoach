<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Actions;

use Illuminate\Mail\Events\MessageSent;

class StoreTransportMessageId
{
    public function handle(MessageSent $event): void
    {
        if (! isset($event->data['send'])) {
            return;
        }

        if (! $event->message->getHeaders()->has('X-Mailgun-Message-ID')) {
            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = $event->data['send'];

        $transportMessageId = $event->message->getHeaders()->get('X-Mailgun-Message-ID')->getBodyAsString();

        $transportMessageId = ltrim($transportMessageId, '<');
        $transportMessageId = rtrim($transportMessageId, '>');

        $send->storeTransportMessageId($transportMessageId);
    }
}
