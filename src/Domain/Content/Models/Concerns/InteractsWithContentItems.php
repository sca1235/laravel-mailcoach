<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait InteractsWithContentItems
{
    public static function bootInteractsWithContentItems(): void
    {
        static::created(function (HasContentItems $model) {
            if (! $model->contentItem) {
                $contentItem = $model->contentItem()->firstOrCreate();
                $model->setRelation('contentItem', $contentItem);
            }
        });

        static::deleting(function (HasContentItems $model) {
            $model->contentItem?->delete();
        });
    }

    public function contentItem(): MorphOne
    {
        return $this->morphOne(self::getContentItemClass(), 'model');
    }

    public function contentItems(): MorphMany
    {
        return $this->morphMany(self::getContentItemClass(), 'model');
    }

    public function sentToNumberOfSubscribers(): int
    {
        return $this->contentItems->sum('sent_to_number_of_subscribers');
    }

    public function openCount(): int
    {
        return $this->contentItems->sum('open_count');
    }

    public function uniqueOpenCount(): int
    {
        return $this->contentItems->sum('unique_open_count');
    }

    public function openRate(): int
    {
        if ($this->sentToNumberOfSubscribers() === 0) {
            return 0;
        }

        return (int) (round($this->uniqueOpenCount() / $this->sentToNumberOfSubscribers(), 4) * 10_000);
    }

    public function clickCount(): int
    {
        return $this->contentItems->sum('click_count');
    }

    public function uniqueClickCount(): int
    {
        return $this->contentItems->sum('unique_click_count');
    }

    public function clickRate(): int
    {
        if ($this->sentToNumberOfSubscribers() === 0) {
            return 0;
        }

        return (int) (round($this->uniqueClickCount() / $this->sentToNumberOfSubscribers(), 4) * 10_000);
    }

    public function unsubscribeCount(): int
    {
        return $this->contentItems->sum('unsubscribe_count');
    }

    public function unsubscribeRate(): int
    {
        if ($this->sentToNumberOfSubscribers() === 0) {
            return 0;
        }

        return (int) (round($this->unsubscribeCount() / $this->sentToNumberOfSubscribers(), 4) * 10_000);
    }

    public function bounceCount(): int
    {
        return $this->contentItems->sum('bounce_count');
    }

    public function bounceRate(): int
    {
        if ($this->sentToNumberOfSubscribers() === 0) {
            return 0;
        }

        return (int) (round($this->bounceCount() / $this->sentToNumberOfSubscribers(), 4) * 10_000);
    }
}
