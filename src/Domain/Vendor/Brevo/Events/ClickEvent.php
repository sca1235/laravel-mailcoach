<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ClickEvent extends BrevoEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'click';
    }

    public function handle(Send $send): void
    {
        $url = Arr::get($this->payload, 'link');

        $send->registerClick($url, $this->getTimestamp());
    }
}
