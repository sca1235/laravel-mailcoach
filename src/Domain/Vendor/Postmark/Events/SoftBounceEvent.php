<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Events;

use Carbon\Carbon;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Enums\BounceType;

class SoftBounceEvent extends PostmarkEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === 'Bounce' &&
            in_array($this->payload['Type'], BounceType::softBounces(), true);
    }

    public function handle(Send $send): void
    {
        $bouncedAt = Carbon::parse($this->payload['BouncedAt'] ?? $this->payload['ChangedAt']);

        $send->registerBounce(
            bouncedAt: $bouncedAt,
            softBounce: true,
            extraAttributes: array_filter([
                'details' => $this->payload['Details'] ?? null,
            ]),
        );
    }
}
