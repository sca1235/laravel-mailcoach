<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignBouncesQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public int $totalCount;

    public function __construct(Campaign $campaign, ?Request $request = null)
    {
        $prefix = DB::getTablePrefix();

        $campaignTable = static::getCampaignTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();
        $sendTable = static::getSendTableName();
        $feedBackTable = static::getSendFeedbackItemTableName();
        $contentItemTable = static::getContentItemTableName();

        /** @var Builder<Campaign> $query */
        $query = static::getContentItemClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$sendTable}.subscriber_id) as bounce_count,
                min({$prefix}{$feedBackTable}.created_at) AS created_at,
                {$prefix}{$feedBackTable}.type as type
            ")
            ->join($sendTable, $contentItemTable.'.id', '=', "{$sendTable}.content_item_id")
            ->join($feedBackTable, $sendTable.'.id', '=', "{$feedBackTable}.send_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$sendTable}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->whereIn("{$contentItemTable}.id", $campaign->contentItems->pluck('id'));

        $this->totalCount = $query->count();

        parent::__construct($query, $request);

        $this
            ->defaultSort('-created_at')
            ->allowedSorts('email', 'bounce_count', 'created_at')
            ->groupBy('subscriber_uuid', 'subscriber_email_list_uuid', 'subscriber_email', 'type')
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'email'
                    )
                ),
                AllowedFilter::exact('type', "{$prefix}{$feedBackTable}.type")
            );
    }
}
