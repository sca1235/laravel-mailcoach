<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OtherEvent extends ResendEvent
{
    public function canHandlePayload(): bool
    {
        return true;
    }

    public function handle(Send $send): void
    {

    }
}
