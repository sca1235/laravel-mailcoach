<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Data\SubscriberClickedAutomationMailLinkQueryConditionData;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberClickedAutomationMailLinkQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_clicked_automation_mail_link';

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
        return 'mailcoach::subscriber-clicked-automation-mail-link-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value, ?TagSegment $tagSegment): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        if (! $value instanceof SubscriberClickedAutomationMailLinkQueryConditionData) {
            throw ConditionException::unsupportedValue($value);
        }

        $automationMailClass = self::getAutomationMailClass();
        $automationMailMorphClass = (new $automationMailClass)->getMorphClass();

        // @todo performance issues ?
        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('clicks.link.contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                $query
                    ->where('model_id', $value->automationMailId)
                    ->where('model_type', $automationMailMorphClass);
            });
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('clicks.link.contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                $query
                    ->where('model_id', $value->automationMailId)
                    ->where('model_type', $automationMailMorphClass);
            });
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('clicks.link', function (Builder $query) use ($value, $automationMailMorphClass) {
                    $query->whereNot('url', $value->link);
                    $query->whereHas('contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                        $query
                            ->where('model_id', $value->automationMailId)
                            ->where('model_type', $automationMailMorphClass);
                    });
                })
                ->orWhereDoesntHave('clicks.link.contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                    $query
                        ->where('model_id', $value->automationMailId)
                        ->where('model_type', $automationMailMorphClass);
                });
        }

        return $baseQuery
            ->whereHas('clicks.link', function (Builder $query) use ($operator, $automationMailMorphClass, $value) {
                $query->where('url', $operator->toSymbol(), $value->link);
                $query->whereHas('contentItem', function (Builder $query) use ($value, $automationMailMorphClass) {
                    $query
                        ->where('model_id', $value->automationMailId)
                        ->where('model_type', $automationMailMorphClass);
                });
            });
    }

    public function dto(): string
    {
        return SubscriberClickedAutomationMailLinkQueryConditionData::class;
    }
}
