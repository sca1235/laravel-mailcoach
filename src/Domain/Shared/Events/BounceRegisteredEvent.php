<?php

namespace Spatie\Mailcoach\Domain\Shared\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class BounceRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
