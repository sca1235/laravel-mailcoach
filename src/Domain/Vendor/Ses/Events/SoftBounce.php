<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Ses\Enums\BounceType;

class SoftBounce extends SesEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->payload['eventType'] !== 'Bounce') {
            return false;
        }

        if (in_array($this->payload['bounce']['bounceType'], BounceType::softBounces(), true)) {
            return true;
        }

        return false;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp(), softBounce: true);
    }
}
