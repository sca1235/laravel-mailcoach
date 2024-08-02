<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberOpenedCampaignQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_opened_campaign';

    public function key(): string
    {
        return self::KEY;
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::Any,
            ComparisonOperator::None,
            ComparisonOperator::Equals,
            ComparisonOperator::NotEquals,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Actions;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-opened-campaign-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value, ?TagSegment $tagSegment): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        $campaignClass = self::getCampaignClass();
        $campaignMorphClass = (new $campaignClass)->getMorphClass();

        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('opens.contentItem', function (Builder $query) use ($campaignMorphClass) {
                $query->where('model_type', $campaignMorphClass);
            });
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('opens.contentItem', function (Builder $query) use ($campaignMorphClass) {
                $query->where('model_type', $campaignMorphClass);
            });
        }

        if (! is_int($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        $contentItems = self::getContentItemClass()::query()
            ->where('model_id', $value)
            ->where('model_type', $campaignMorphClass)
            ->select('id')
            ->get();

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery->whereDoesntHave('opens', function (Builder $query) use ($contentItems) {
                $query->whereIn('content_item_id', $contentItems->pluck('id'));
            });
        }

        return $baseQuery->whereHas('opens', function (Builder $query) use ($contentItems) {
            $query->whereIn('content_item_id', $contentItems->pluck('id'));
        });
    }

    public function dto(): ?string
    {
        return null;
    }
}
