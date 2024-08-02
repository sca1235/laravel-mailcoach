<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SubscriberTagsQueryCondition extends QueryCondition
{
    use UsesMailcoachModels;

    public const KEY = 'subscriber_tags';

    public function key(): string
    {
        return self::KEY;
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::In,
            ComparisonOperator::NotIn,
            ComparisonOperator::All,
            ComparisonOperator::None,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Tags;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-tags-condition';
    }

    /**
     * @param  Builder<Subscriber>  $baseQuery
     */
    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value, ?TagSegment $tagSegment): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        $values = Arr::wrap($value);

        $prefix = DB::getTablePrefix();

        return match ($operator) {
            ComparisonOperator::All => $baseQuery
                ->where(
                    DB::connection(Mailcoach::getDatabaseConnection())
                        ->table('mailcoach_email_list_subscriber_tags')
                        ->selectRaw('count(*)')
                        ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                        ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $values),
                    '>=', count($values)
                ),
            ComparisonOperator::In => $baseQuery->addWhereExistsQuery(DB::connection(Mailcoach::getDatabaseConnection())->table('mailcoach_email_list_subscriber_tags')
                ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $values)
            ),
            ComparisonOperator::NotIn => $baseQuery->addWhereExistsQuery(DB::connection(Mailcoach::getDatabaseConnection())->table('mailcoach_email_list_subscriber_tags')
                ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $values),
                not: true
            ),
            ComparisonOperator::None => $baseQuery
                ->where(
                    DB::connection(Mailcoach::getDatabaseConnection())
                        ->table('mailcoach_email_list_subscriber_tags')
                        ->selectRaw('count(*)')
                        ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                        ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $values),
                    '<', count($values)
                ),
            default => $baseQuery,
        };
    }

    public function dto(): ?string
    {
        return null;
    }
}
