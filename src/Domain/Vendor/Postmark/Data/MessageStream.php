<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Data;

class MessageStream
{
    public function __construct(
        public string $id,
        public string $serverId,
        public string $name,
    ) {
    }
}
