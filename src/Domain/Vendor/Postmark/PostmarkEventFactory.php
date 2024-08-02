<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark;

use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\PermanentBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\PostmarkEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\SoftBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\SubscriptionChangeEvent;

class PostmarkEventFactory
{
    protected static array $postmarkEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        PermanentBounceEvent::class,
        SoftBounceEvent::class,
        SubscriptionChangeEvent::class,
    ];

    public static function createForPayload(array $payload): PostmarkEvent
    {
        $postmarkEvent = collect(static::$postmarkEvents)
            ->map(fn (string $postmarkEventClass) => new $postmarkEventClass($payload))
            ->first(fn (PostmarkEvent $postmarkEvent) => $postmarkEvent->canHandlePayload());

        return $postmarkEvent ?? new OtherEvent($payload);
    }
}
