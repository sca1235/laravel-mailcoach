<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintEvent extends MailgunEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'complained';
    }

    public function handle(Send $send): void
    {
        $send->registerComplaint($this->getTimestamp());
    }
}
