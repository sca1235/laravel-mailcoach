<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OpenEvent extends MailgunEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'opened';
    }

    public function handle(Send $send): ?Open
    {
        return $send->registerOpen($this->getTimestamp());
    }
}
