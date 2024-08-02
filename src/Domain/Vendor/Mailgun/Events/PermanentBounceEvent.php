<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PermanentBounceEvent extends MailgunEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->event !== 'failed') {
            return false;
        }

        if (Arr::get($this->payload, 'event-data.severity') !== 'permanent') {
            return false;
        }

        return true;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp());
    }
}
