<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid;

use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\PermanentBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\SendgridEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Events\SoftBounceEvent;

class SendgridEventFactory
{
    protected static array $sendgridEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        PermanentBounceEvent::class,
        SoftBounceEvent::class,
    ];

    public static function createForPayload(array $payload): SendgridEvent
    {
        $sendgridEvent = collect(static::$sendgridEvents)
            ->map(fn (string $sendgridEventClass) => new $sendgridEventClass($payload))
            ->first(fn (SendgridEvent $sendgridEvent) => $sendgridEvent->canHandlePayload());

        return $sendgridEvent ?? new OtherEvent($payload);
    }
}
