<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionalMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        parent::__construct(self::getTransactionalMailLogItemClass()::query(), $request);

        $filterFields = array_map('trim', config('mailcoach.transactional.search_fields', ['contentItem.subject']));

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(
                'subject',
                'created_at',
                'id',
            )
            ->allowedFilters(
                AllowedFilter::callback('transport_message_id', function (Builder $query, $value) {
                    $query->whereHas('contentItem.sends', function (Builder $query) use ($value) {
                        $query->where('transport_message_id', $value);
                    });
                }),
                AllowedFilter::custom('search', new FuzzyFilter(...$filterFields)),
            );
    }
}
