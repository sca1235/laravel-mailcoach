<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class BounceEvent extends ResendEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'email.bounced';
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp());
    }
}
