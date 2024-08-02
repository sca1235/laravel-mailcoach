<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintEvent extends SendgridEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'spamreport';
    }

    public function handle(Send $send): void
    {
        if (Arr::get($this->payload, 'email') !== $send->subscriber->email) {
            return;
        }

        $send->registerComplaint($this->getTimestamp());
    }
}
