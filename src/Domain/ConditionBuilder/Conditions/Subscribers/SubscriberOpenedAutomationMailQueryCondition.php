<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberOpenedAutomationMailQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_opened_automation_mail';

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
        return 'mailcoach::subscriber-opened-automation-mail-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value, ?TagSegment $tagSegment): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        $automationMailClass = self::getAutomationMailClass();
        $automationMailMorphClass = (new $automationMailClass)->getMorphClass();

        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass) {
                $query->where('model_type', $automationMailMorphClass);
            });
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('opens.contentItem', function (Builder $query) use ($automationMailMorphClass) {
                $query->where('model_type', $automationMailMorphClass);
            });
        }

        if (! is_int($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass, $value) {
                    $query
                        ->where('model_id', '!=', $value)
                        ->where('model_type', $automationMailMorphClass);
                })->orWhereDoesntHave('opens.contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                    $query
                        ->where('model_id', $value)
                        ->where('model_type', $automationMailMorphClass);
                });
        }

        return $baseQuery->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass, $value) {
            $query
                ->where('model_id', $value)
                ->where('model_type', $automationMailMorphClass);
        });
    }

    public function dto(): ?string
    {
        return null;
    }
}
