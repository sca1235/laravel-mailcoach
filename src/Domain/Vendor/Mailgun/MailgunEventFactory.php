<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun;

use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\MailgunEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\PermanentBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\SoftBounceEvent;

class MailgunEventFactory
{
    protected static array $mailgunEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        PermanentBounceEvent::class,
        SoftBounceEvent::class,
    ];

    public static function createForPayload(array $payload): MailgunEvent
    {
        $mailgunEvent = collect(static::$mailgunEvents)
            ->map(fn (string $mailgunEventClass) => new $mailgunEventClass($payload))
            ->first(fn (MailgunEvent $mailgunEvent) => $mailgunEvent->canHandlePayload());

        return $mailgunEvent ?? new OtherEvent($payload);
    }
}
