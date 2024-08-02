<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class SubscriberSuppressedEvent
{
    public function __construct(
        public Subscriber $subscriber
    ) {
    }
}
