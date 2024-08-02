<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;

class SubscriberSubscribedAtQueryCondition extends QueryCondition
{
    public function key(): string
    {
        return 'subscriber_subscribed_at';
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::GreaterThanOrEquals,
            ComparisonOperator::SmallerThanOrEquals,
            ComparisonOperator::Between,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Attributes;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-subscribed-at-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value, ?TagSegment $tagSegment): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        if ($operator === ComparisonOperator::Between) {
            return $this->applyBetweenOperator($baseQuery, $value);
        }

        $date = CarbonImmutable::parse($value);

        if ($date === null) {
            throw new RuntimeException('Invalid date format.');
        }

        return $baseQuery
            ->whereDate('subscribed_at', $operator->toSymbol(), $date);
    }

    protected function applyBetweenOperator(Builder $baseQuery, mixed $value): Builder
    {
        $startDate = CarbonImmutable::parse($value[0]);
        $endDate = CarbonImmutable::parse($value[1]);

        if ($startDate === null || $endDate === null) {
            throw new RuntimeException('Invalid date format.');
        }

        return $baseQuery
            ->whereBetween('subscribed_at', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ]);
    }

    public function dto(): ?string
    {
        return null;
    }
}
