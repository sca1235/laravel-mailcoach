<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Actions;

use Illuminate\Mail\Events\MessageSent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class StoreTransportMessageId
{
    public function handle(MessageSent $event): void
    {
        if (! isset($event->data['send'])) {
            return;
        }

        if (! $event->message->getHeaders()->has('X-Ses-Message-ID')) {
            return;
        }

        /** @var Send $send */
        $send = $event->data['send'];

        $transportMessageId = $event->message->getHeaders()->get('X-Ses-Message-ID')->getBodyAsString();

        $send->storeTransportMessageId($transportMessageId);
    }
}
