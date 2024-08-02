<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class RetrySendingFailedSendsAction
{
    public function execute(Campaign $campaign): int
    {
        return $campaign->contentItems->sum(function (ContentItem $contentItem) {
            return $contentItem->sends()->getQuery()->failed()->update([
                'sent_at' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ]);
        });
    }
}
