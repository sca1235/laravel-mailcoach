<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class Complaint extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Complaint';
    }

    public function handle(Send $send): void
    {
        $send->registerComplaint($this->getTimestamp());
    }
}
