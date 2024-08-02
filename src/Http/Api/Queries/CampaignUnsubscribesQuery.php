<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignUnsubscribesQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(Campaign $campaign, ?Request $request = null)
    {
        parent::__construct(
            self::getUnsubscribeClass()::whereIn('content_item_id', $campaign->contentItems->pluck('id')),
            $request,
        );

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts('created_at', 'id')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(
                    'subscriber.email',
                    'subscriber.first_name',
                    'subscriber.last_name'
                ))
            );
    }
}
