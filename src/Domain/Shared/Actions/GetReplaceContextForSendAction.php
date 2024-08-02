<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class GetReplaceContextForSendAction
{
    public function __construct(
        protected GetReplaceContextForSendableAction $getReplaceContextForSendableAction,
        protected GetReplaceContextForSubscriberAction $getReplaceContextForSubscriberAction,
    ) {
    }

    public function execute(?Send $send): array
    {
        if (! $send) {
            return [];
        }

        $context = [
            'sendUuid' => $send->uuid,
        ];

        $context = array_merge($context, $this->getReplaceContextForSendableAction->execute($send->getSendable()));
        $context = array_merge($context, $this->getReplaceContextForSubscriberAction->execute($send->subscriber, $send));

        return $context;
    }
}
