<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintEvent extends BrevoEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'complaint' || $this->event === 'spam';
    }

    public function handle(Send $send)
    {
        $send->registerComplaint($this->getTimestamp());
    }
}
