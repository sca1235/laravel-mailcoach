<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SendsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request)
    {
        $query = self::getSendClass()::query()
            ->with([
                'contentItem.model',
                'opens',
                'clicks',
            ]);

        parent::__construct($query, $request);

        $this
            ->defaultSort('-sent_at')
            ->allowedSorts('sent_at')
            ->allowedFilters(
                AllowedFilter::callback('subscriber_uuid', function (Builder $query, string $uuid) {
                    return $query->whereHas('subscriber', function (Builder $query) use ($uuid) {
                        return $query->where('uuid', $uuid);
                    });
                }),
                AllowedFilter::callback('campaign_uuid', function (Builder $query, string $uuid) {
                    return $query->whereHas('contentItem', function (Builder $query) use ($uuid) {
                        $query->where('model_type', (new (self::getCampaignClass()))->getMorphClass())
                            ->whereHas('model', function (Builder $query) use ($uuid) {
                                return $query->where('uuid', $uuid);
                            });
                    });
                }),
                AllowedFilter::callback('automation_mail_uuid', function (Builder $query, string $uuid) {
                    return $query->whereHas('contentItem', function (Builder $query) use ($uuid) {
                        $query->where('model_type', (new (self::getAutomationMailClass()))->getMorphClass())
                            ->whereHas('model', function (Builder $query) use ($uuid) {
                                return $query->where('uuid', $uuid);
                            });
                    });
                }),
                AllowedFilter::callback('transactional_mail_log_item_uuid', function (Builder $query, string $uuid) {
                    return $query->whereHas('contentItem', function (Builder $query) use ($uuid) {
                        $query->where('model_type', (new (self::getTransactionalMailLogItemClass()))->getMorphClass())
                            ->whereHas('model', function (Builder $query) use ($uuid) {
                                return $query->where('uuid', $uuid);
                            });
                    });
                })
            );
    }
}
