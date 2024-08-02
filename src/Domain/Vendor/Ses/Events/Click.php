<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class Click extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Click';
    }

    public function handle(Send $send): void
    {
        $send->registerClick(
            $this->payload['click']['link'],
            $this->getTimestamp()
        );
    }
}
