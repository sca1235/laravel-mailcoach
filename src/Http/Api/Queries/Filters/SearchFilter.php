<?php

namespace Spatie\Mailcoach\Http\Api\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\Filters\Filter;

class SearchFilter implements Filter
{
    use UsesMailcoachModels;

    /** @var string[] */
    protected array $fields;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $clone = clone $query;
        $query->whereIn(self::getSubscriberTableName().'.id', $clone->emailSearch($value)->select(self::getSubscriberTableName().'.id'));

        return $query;
    }
}
