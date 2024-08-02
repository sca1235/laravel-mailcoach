<?php

namespace Spatie\Mailcoach\Http\Api\Queries\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class CampaignSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $sortDirection = $descending ? 'DESC' : 'ASC';
        $reverseSortDirection = $descending ? 'ASC' : 'DESC';

        $orderClause = <<<SQL
            CASE
                WHEN status = 'draft' AND scheduled_at IS NULL THEN 0
                ELSE 1
            END $reverseSortDirection
            ,
            CASE
                WHEN scheduled_at IS NOT NULL THEN scheduled_at
                WHEN sent_at IS NOT NULL THEN sent_at
                ELSE updated_at
            END $sortDirection
        SQL;

        $query->orderByRaw($orderClause);
    }
}
