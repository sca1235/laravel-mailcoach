<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Events;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

abstract class PostmarkEvent
{
    protected array $payload;

    protected string $event;

    public function __construct(array $payload)
    {
        $this->payload = $payload;

        $this->event = Arr::get($payload, 'RecordType', '');
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(Send $send);
}
