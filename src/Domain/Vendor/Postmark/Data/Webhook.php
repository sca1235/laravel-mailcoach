<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Data;

class Webhook
{
    public function __construct(
        public string $id,
        public string $url,
        public string $streamId,
        public array $triggers,
    ) {
    }

    public static function fromPayload(array $data): self
    {
        return new self(
            $data['ID'],
            $data['Url'],
            $data['MessageStream'],
            $data['Triggers'],
        );
    }
}
