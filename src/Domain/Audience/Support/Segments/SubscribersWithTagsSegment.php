<?php

namespace Spatie\Mailcoach\Domain\Audience\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment as TagSegmentModel;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SubscribersWithTagsSegment extends Segment
{
    use UsesMailcoachModels;

    public function description(): string
    {
        if ($this->getTagSegmentModel()) {
            return $this->getTagSegmentModel()->name;
        }

        return (string) __mc('deleted segment');
    }

    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $tagSegment = $this->getTagSegmentModel();

        if (! $tagSegment) {
            return;
        }

        $tagSegment->applyConditionBuilder($subscribersQuery);

        $subscribersQuery->where(self::getSubscriberTableName().'.email_list_id', $tagSegment->email_list_id);
    }

    public function getTagSegmentModel(): ?TagSegmentModel
    {
        return self::getTagSegmentClass()::find($this->segmentable->segment_id);
    }
}
