<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Enums\BounceType;

class SoftBounceEvent extends BrevoEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === BounceType::Soft->value;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp(), softBounce: true);
    }
}
