<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintEvent extends ResendEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'email.complained';
    }

    public function handle(Send $send): void
    {
        $send->registerComplaint($this->getTimestamp());
    }
}
