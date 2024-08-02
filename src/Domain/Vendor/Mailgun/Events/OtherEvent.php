<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OtherEvent extends MailgunEvent
{
    public function canHandlePayload(): bool
    {
        return true;
    }

    public function handle(Send $send)
    {
    }
}
