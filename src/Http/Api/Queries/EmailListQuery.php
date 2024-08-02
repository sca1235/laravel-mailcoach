<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        $prefix = DB::getTablePrefix();

        $query = self::getEmailListClass()::query();

        if ($request && str_contains($request->get('sort'), 'active_subscribers_count')) {
            $query->join(self::getSubscriberTableName(), self::getSubscriberTableName().'.email_list_id', self::getEmailListTableName().'.id')
                ->addSelect(DB::raw('count('.$prefix.self::getSubscriberTableName().'.id) as active_subscribers_count'))
                ->addSelect(self::getEmailListTableName().'.*')
                ->groupBy(self::getEmailListTableName().'.id');
        }

        parent::__construct($query, $request);

        $this
            ->defaultSort('name')
            ->allowedSorts('name', 'created_at', 'active_subscribers_count')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            );
    }
}
