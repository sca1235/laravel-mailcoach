<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Spatie\Mailcoach\Mailcoach;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportCampaignsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $contentItemMapping = [];

    private int $total = 0;

    private int $index = 0;

    public function name(): string
    {
        return 'Campaigns & Campaign Links';
    }

    public function execute(): void
    {
        $this->total = $this->getMeta('campaigns_count', 0) + $this->getMeta('campaign_links_count', 0);

        $this->importCampaigns();
        $this->importCampaignLinks();
    }

    private function importCampaigns(): void
    {
        if (! $this->importDisk->exists('import/campaigns.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/campaigns.csv', $this->importDisk->readStream('import/campaigns.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/campaigns.csv'));

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $columns = Schema::connection(Mailcoach::getDatabaseConnection())->getColumnListing(self::getCampaignTableName());
        $contentItemColumns = Schema::connection(Mailcoach::getDatabaseConnection())->getColumnListing(self::getContentItemTableName());

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $emailLists[$row['email_list_uuid']];
            $row['segment_id'] = self::getTagSegmentClass()::where('name', $row['segment_name'])->where('email_list_id', $row['email_list_id'])->first()?->id;
            $row['all_sends_created_at'] ??= $row['status'] !== 'draft' ? now() : null;
            $row['all_sends_dispatched_at'] ??= $row['status'] !== 'draft' ? now() : null;

            $campaign = self::getCampaignClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except(Arr::only($row, $columns), ['id', 'email_list_uuid', 'segment_name'])),
            );

            $contentAttributes = array_filter(Arr::except(Arr::only($row, $contentItemColumns), ['id', 'model_id']));
            $contentAttributes['uuid'] = $row['content_item_uuid'] ?? $campaign->contentItem->uuid;
            $campaign->contentItem->update($contentAttributes);

            $this->contentItemMapping[$row['content_item_id']] = $campaign->contentItem->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/campaigns.csv');
    }

    private function importCampaignLinks(): void
    {
        if (! $this->importDisk->exists('import/campaign_links.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/campaign_links.csv', $this->importDisk->readStream('import/campaign_links.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/campaign_links.csv'));

        foreach ($reader->getRows() as $campaignLinkData) {
            $campaignLinkData['content_item_id'] = $this->contentItemMapping[$campaignLinkData['content_item_id']];

            self::getLinkClass()::firstOrCreate(
                ['content_item_id' => $campaignLinkData['content_item_id'], 'url' => $campaignLinkData['url']],
                array_filter(Arr::except($campaignLinkData, ['id'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/campaign_links.csv');
    }
}
