<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class OpenEvent extends SendgridEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'open';
    }

    public function handle(Send $send): void
    {
        $email = Arr::get($this->payload, 'email');

        if ($send->subscriber && $email !== $send->subscriber->email) {
            return;
        }

        if ($send->contentItem->model instanceof TransactionalMailLogItem && $email !== $send->contentItem->model->to[0]['email']) {
            return;
        }

        $send->registerOpen($this->getTimestamp());
    }
}
