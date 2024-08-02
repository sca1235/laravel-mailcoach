<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Events;

use Carbon\Carbon;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Enums\BounceType;

class PermanentBounceEvent extends PostmarkEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->event === 'Bounce' && $this->payload['Type'] === BounceType::HardBounce->value) {
            return true;
        }

        if ($this->event === 'SubscriptionChange' && ($this->payload['SuppressionReason'] ?? null) === BounceType::HardBounce->value) {
            return true;
        }

        return false;
    }

    public function handle(Send $send): void
    {
        $bouncedAt = Carbon::parse($this->payload['BouncedAt'] ?? $this->payload['ChangedAt']);

        $send->registerBounce(
            bouncedAt: $bouncedAt,
            extraAttributes: array_filter([
                'details' => $this->payload['Details'] ?? null,
            ]),
        );
    }
}
