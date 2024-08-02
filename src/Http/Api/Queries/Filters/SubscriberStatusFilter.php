<?php

namespace Spatie\Mailcoach\Http\Api\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\QueryBuilder\Filters\Filter;

class SubscriberStatusFilter implements Filter
{
    /** @param  Builder<Subscriber>  $query */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'unconfirmed') {
            return $query->unconfirmed();
        }

        if ($value === 'subscribed') {
            return $query->subscribed();
        }

        if ($value === 'unsubscribed') {
            return $query->unsubscribed();
        }

        return $query;
    }
}
