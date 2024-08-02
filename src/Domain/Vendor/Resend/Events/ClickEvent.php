<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ClickEvent extends ResendEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'email.clicked';
    }

    public function handle(Send $send): void
    {
        $url = Arr::get($this->payload, 'data.click.link');

        $send->registerClick($url, $this->getTimestamp());
    }
}
