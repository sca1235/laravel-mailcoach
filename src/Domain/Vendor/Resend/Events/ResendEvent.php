<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Events;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

abstract class ResendEvent
{
    protected array $payload;

    protected string $event;

    public function __construct(array $payload)
    {
        $this->payload = $payload;

        $this->event = Arr::get($payload, 'type');
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(Send $send);

    public function getTimestamp(): ?DateTimeInterface
    {
        $timestamp = Arr::get($this->payload, 'created_at');

        return $timestamp ? Carbon::parse($timestamp) : null;
    }
}
