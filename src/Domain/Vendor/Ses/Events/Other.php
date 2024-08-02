<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class Other extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return true;
    }

    public function handle(Send $send): void
    {
    }
}
