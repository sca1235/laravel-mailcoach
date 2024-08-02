<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property string $name
 * @property ?\Spatie\Mailcoach\Domain\Content\Models\ContentItem $contentItem
 * @property \Illuminate\Support\Collection<\Spatie\Mailcoach\Domain\Content\Models\ContentItem> $contentItems
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface HasContentItems
{
    public function contentItem(): MorphOne;

    public function contentItems(): MorphMany;

    public function sentToNumberOfSubscribers(): int;

    public function openCount(): int;

    public function uniqueOpenCount(): int;

    public function openRate(): int;

    public function clickCount(): int;

    public function uniqueClickCount(): int;

    public function clickRate(): int;

    public function unsubscribeCount(): int;

    public function unsubscribeRate(): int;

    public function bounceCount(): int;

    public function bounceRate(): int;
}
