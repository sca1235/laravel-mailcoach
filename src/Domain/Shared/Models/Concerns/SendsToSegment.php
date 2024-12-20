<?php

namespace Spatie\Mailcoach\Domain\Shared\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;

/**
 * @property string $segment_class
 * @property \Spatie\Mailcoach\Domain\Audience\Models\EmailList|null $emailList
 */
trait SendsToSegment
{
    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function tagSegment(): BelongsTo
    {
        return $this->belongsTo(TagSegment::class);
    }

    public function segment(Segment|string $segmentClassOrObject): self
    {
        if (! is_a($segmentClassOrObject, Segment::class, true)) {
            throw CouldNotSendCampaign::invalidSegmentClass($this, $segmentClassOrObject);
        }

        $this->update(['segment_class' => serialize($segmentClassOrObject)]);

        return $this;
    }

    public function getSegment(): Segment
    {
        $segmentClass = $this->segment_class ?? EverySubscriberSegment::class;

        if (str_contains($segmentClass, ':')) {
            $segmentClass = rescue(
                fn () => unserialize($segmentClass) ?: $segmentClass,
                $segmentClass,
                false,
            );
        }

        if ($segmentClass instanceof Segment) {
            return $segmentClass->setSegmentable($this);
        }

        return app($segmentClass)->setSegmentable($this);
    }

    public function segmentSubscriberCount(): int
    {
        return cache()->remember("segmentSubscriberCount-{$this->id}", now()->addSeconds(10), function () {
            if (! $this->emailList) {
                return 0;
            }

            return tap($this->baseSubscribersQuery(), function (Builder $query) {
                $this->getSegment()->subscribersQuery($query);
            })->count();
        });
    }

    /** @return Builder<Subscriber> */
    public function baseSubscribersQuery(): Builder
    {
        return $this
            ->emailList
            ->subscribers()
            ->subscribed()
            ->getQuery();
    }

    public function usesSegment(): bool
    {
        return $this->segment_class && $this->segment_class !== EverySubscriberSegment::class;
    }

    public function segmentingOnSubscriberTags(): bool
    {
        return $this->segment_class === SubscribersWithTagsSegment::class;
    }

    public function notSegmenting(): bool
    {
        return is_null($this->segment_class)
            || $this->segment_class === EverySubscriberSegment::class;
    }

    public function usingCustomSegment(): bool
    {
        if (is_null($this->segment_class)) {
            return false;
        }

        return ! in_array($this->segment_class, [
            SubscribersWithTagsSegment::class,
            EverySubscriberSegment::class,
        ]);
    }
}
