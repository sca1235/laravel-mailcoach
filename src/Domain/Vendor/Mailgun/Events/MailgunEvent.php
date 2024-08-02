<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Events;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

abstract class MailgunEvent
{
    protected array $payload;

    protected string $event;

    public function __construct(array $payload)
    {
        $this->payload = $payload;

        $this->event = Arr::get($payload, 'event-data.event');
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(Send $send);

    public function getTimestamp(): ?DateTimeInterface
    {
        $timestamp = $this->payload['event-data']['timestamp'];

        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
