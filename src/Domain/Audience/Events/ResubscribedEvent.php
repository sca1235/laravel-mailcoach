<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class ResubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
    ) {
    }
}
