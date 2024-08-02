<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Traits\Conditionable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class QueryCondition implements Condition
{
    use Conditionable;
    use UsesMailcoachModels;

    abstract public function apply(
        Builder $baseQuery,
        ComparisonOperator $operator,
        mixed $value,
        ?TagSegment $tagSegment,
    ): Builder;
}
