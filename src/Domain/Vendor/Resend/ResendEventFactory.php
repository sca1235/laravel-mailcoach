<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend;

use Spatie\Mailcoach\Domain\Vendor\Resend\Events\BounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Events\ResendEvent;

class ResendEventFactory
{
    protected static array $resendEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        BounceEvent::class,
    ];

    public static function createForPayload(array $payload): ResendEvent
    {
        $resendEvent = collect(static::$resendEvents)
            ->map(fn (string $resendEventClass) => new $resendEventClass($payload))
            ->first(fn (ResendEvent $resendEvent) => $resendEvent->canHandlePayload());

        return $resendEvent ?? new OtherEvent($payload);
    }
}
