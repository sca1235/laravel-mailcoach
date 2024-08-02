<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SegmentsQuery extends QueryBuilder
{
    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        $query = $emailList->segments()->getQuery();

        parent::__construct($query, $request);

        $this
            ->defaultSort('name')
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            )
            ->allowedIncludes(['emailList']);
    }
}
