<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class DetermineSplitTestWinnerAction
{
    public function execute(Campaign $campaign): Campaign
    {
        $winningContentItem = $this->getWinningContentItem($campaign);

        $campaign->update([
            'split_test_winning_content_item_id' => $winningContentItem->id,
        ]);

        return $campaign;
    }

    protected function getWinningContentItem(Campaign $campaign): ContentItem
    {
        $contentItemStats = $campaign->contentItems->map(function (ContentItem $contentItem) {
            return array_merge($contentItem->getStatsBefore(), ['content_item_id' => $contentItem->id]);
        });

        // First check unique clicks
        $maxClickCount = $contentItemStats->max('unique_click_count');

        if ($maxClickCount > 0 && $contentItemStats->where('unique_click_count', $maxClickCount)->count() === 1) {
            $contentItemId = $contentItemStats->where('unique_click_count', $maxClickCount)->first()['content_item_id'];

            return $campaign->contentItems->find($contentItemId);
        }

        // If unique clicks are same, check opens
        $maxOpenCount = $contentItemStats->max('unique_open_count');

        if ($maxOpenCount > 0 && $contentItemStats->where('unique_open_count', $maxOpenCount)->count() === 1) {
            $contentItemId = $contentItemStats->where('unique_open_count', $maxOpenCount)->first()['content_item_id'];

            return $campaign->contentItems->find($contentItemId);
        }

        // If we don't have opens or clicks, we probably don't have tracking, so sort by unsubscribes instead
        $contentItemId = $contentItemStats->sortBy('unsubscribe_count')->first()['content_item_id'];

        return $campaign->contentItems->find($contentItemId);
    }
}
