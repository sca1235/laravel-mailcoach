<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo;

use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\BrevoEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\PermanentBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Events\SoftBounceEvent;

class BrevoEventFactory
{
    protected static array $brevoEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        PermanentBounceEvent::class,
        SoftBounceEvent::class,
    ];

    public static function createForPayload(array $payload): BrevoEvent
    {
        $brevoEvent = collect(static::$brevoEvents)
            ->map(fn (string $brevoEventClass) => new $brevoEventClass($payload))
            ->first(fn (BrevoEvent $brevoEvent) => $brevoEvent->canHandlePayload());

        return $brevoEvent ?? new OtherEvent($payload);
    }
}
