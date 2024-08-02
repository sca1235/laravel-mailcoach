<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Ses\Enums\BounceType;

class PermanentBounce extends SesEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->payload['eventType'] !== 'Bounce') {
            return false;
        }

        if ($this->payload['bounce']['bounceType'] !== BounceType::Permanent->value) {
            return false;
        }

        return true;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp());
    }
}
