<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailStatisticsCalculatedEvent
{
    public function __construct(
        public TransactionalMail $transactionalMail
    ) {
    }
}
