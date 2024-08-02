<?php

namespace Spatie\Mailcoach\Http\Api\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\QueryBuilder\Filters\Filter;

class CampaignStatusFilter implements Filter
{
    /** @param  Builder<Campaign>  $query */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'sent') {
            return $query->sendingOrSent();
        }

        if ($value === 'scheduled') {
            return $query->scheduled();
        }

        if ($value === 'draft') {
            return $query->draft();
        }

        return $query;
    }
}
