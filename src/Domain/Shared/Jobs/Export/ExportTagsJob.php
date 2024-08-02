<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Mailcoach;

class ExportTagsJob extends ExportJob
{
    /**
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Tags';
    }

    public function execute(): void
    {
        $prefix = DB::getTablePrefix();

        $tags = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getTagTableName())
            ->select(self::getTagTableName().'.*', DB::raw($prefix.self::getEmailListTableName().'.uuid as email_list_uuid'))
            ->leftJoin(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getTagTableName().'.email_list_id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->orWhereNull('email_list_id')
            ->get();

        $this->writeFile('tags.csv', $tags);
        $this->addMeta('tags_count', $tags->count());

        $subscriberTagsCount = 0;
        DB::connection(Mailcoach::getDatabaseConnection())
            ->table('mailcoach_email_list_subscriber_tags')
            ->select(
                'mailcoach_email_list_subscriber_tags.*',
                DB::raw($prefix.self::getSubscriberTableName().'.uuid as subscriber_uuid'),
                DB::raw($prefix.self::getTagTableName().'.name as tag_name'),
                DB::raw($prefix.self::getEmailListTableName().'.uuid as email_list_uuid'),
            )
            ->orderBy('id')
            ->join(self::getSubscriberTableName(), self::getSubscriberTableName().'.id', 'mailcoach_email_list_subscriber_tags.subscriber_id')
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', self::getSubscriberTableName().'.email_list_id')
            ->join(self::getTagTableName(), self::getTagTableName().'.id', 'mailcoach_email_list_subscriber_tags.tag_id')
            ->whereIn(self::getSubscriberTableName().'.email_list_id', $this->selectedEmailLists)
            ->chunk(50_000, function (Collection $subscriberTags, $index) use (&$subscriberTagsCount) {
                $subscriberTagsCount += $subscriberTags->count();
                $this->writeFile("email_list_subscriber_tags-{$index}.csv", $subscriberTags);
            });

        $this->addMeta('email_list_subscriber_tags_count', $subscriberTagsCount);
    }
}
