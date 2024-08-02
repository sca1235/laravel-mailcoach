<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Mailcoach;

class ExportCampaignsJob extends ExportJob
{
    /**
     * @param  array<int>  $selectedCampaigns
     */
    public function __construct(protected string $path, protected array $selectedCampaigns)
    {
    }

    public function name(): string
    {
        return 'Campaigns';
    }

    public function execute(): void
    {
        $prefix = DB::getTablePrefix();

        $campaigns = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getCampaignTableName())
            ->select(
                self::getContentItemTableName().'.*',
                DB::raw($prefix.self::getContentItemTableName().'.id as content_item_id'),
                DB::raw($prefix.self::getContentItemTableName().'.uuid as content_item_uuid'),
                self::getCampaignTableName().'.*',
                DB::raw($prefix.self::getEmailListTableName().'.uuid as email_list_uuid'),
                DB::raw($prefix.self::getTagSegmentTableName().'.name as segment_name'),
            )
            ->join(self::getContentItemTableName(), self::getContentItemTableName().'.model_id', '=', self::getCampaignTableName().'.id')
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getCampaignTableName().'.email_list_id')
            ->leftJoin(self::getTagSegmentTableName(), self::getTagSegmentTableName().'.id', '=', self::getCampaignTableName().'.segment_id')
            ->whereIn(self::getCampaignTableName().'.id', $this->selectedCampaigns)
            ->where(self::getContentItemTableName().'.model_type', (new (self::getCampaignClass()))->getMorphClass())
            ->get();

        $this->writeFile('campaigns.csv', $campaigns);
        $this->addMeta('campaigns_count', $campaigns->count());

        $campaignLinks = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getLinkTableName())
            ->whereIn('content_item_id', $campaigns->pluck('content_item_id'))
            ->get();

        $this->writeFile('campaign_links.csv', $campaignLinks);
        $this->addMeta('campaign_links_count', $campaignLinks->count());
    }
}
