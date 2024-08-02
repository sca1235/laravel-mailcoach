<?php

namespace Spatie\Mailcoach\Domain\Content\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;

class ContentOpenedEvent
{
    public function __construct(
        public Open $open
    ) {
    }
}
