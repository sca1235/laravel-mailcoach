<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OpenEvent extends ResendEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'email.opened';
    }

    public function handle(Send $send): ?Open
    {
        return $send->registerOpen($this->getTimestamp());
    }
}
