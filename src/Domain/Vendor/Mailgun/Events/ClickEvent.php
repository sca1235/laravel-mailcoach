<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ClickEvent extends MailgunEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'clicked';
    }

    public function handle(Send $send): void
    {
        $url = Arr::get($this->payload, 'event-data.url');

        $send->registerClick($url, $this->getTimestamp());
    }
}
