<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OtherEvent extends BrevoEvent
{
    public function canHandlePayload(): bool
    {
        return true;
    }

    public function handle(Send $send): void
    {
    }
}
