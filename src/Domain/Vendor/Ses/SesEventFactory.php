<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses;

use Spatie\Mailcoach\Domain\Vendor\Ses\Events\Click;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\Complaint;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\Open;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\Other;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\PermanentBounce;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\SesEvent;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\SoftBounce;

class SesEventFactory
{
    protected static array $sesEvents = [
        Click::class,
        Complaint::class,
        Open::class,
        PermanentBounce::class,
        SoftBounce::class,
    ];

    public static function createForPayload(array $payload): SesEvent
    {
        $sesEvent = collect(static::$sesEvents)
            ->map(fn (string $sesEventClass) => new $sesEventClass($payload))
            ->first(fn (SesEvent $sesEvent) => $sesEvent->canHandlePayload());

        return $sesEvent ?? new Other($payload);
    }
}
