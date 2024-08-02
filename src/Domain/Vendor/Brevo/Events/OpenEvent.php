<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OpenEvent extends BrevoEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'opened' || $this->event === 'proxy_open';
    }

    public function handle(Send $send): ?Open
    {
        return $send->registerOpen($this->getTimestamp());
    }
}
