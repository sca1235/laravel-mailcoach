<?php

namespace Spatie\Mailcoach\Domain\Content\Events;

use Spatie\Mailcoach\Domain\Content\Models\Click;

class LinkClickedEvent
{
    public function __construct(
        public Click $click
    ) {
    }
}
