<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class Open extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Open';
    }

    public function handle(Send $send): void
    {
        $send->registerOpen($this->getTimestamp());
    }
}
