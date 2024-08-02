<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class ExportSubscribersJob extends ExportJob
{
    use UsesMailcoachModels;

    /**
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $subscribersCount = 0;
        $prefix = DB::getTablePrefix();

        DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getSubscriberTableName())
            ->select(self::getSubscriberTableName().'.*', DB::raw($prefix.self::getEmailListTableName().'.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', self::getSubscriberTableName().'.email_list_id')
            ->orderBy('id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->chunk(10_000, function (Collection $subscribers, $index) use (&$subscribersCount) {
                $subscribersCount += $subscribers->count();

                $this->writeFile("subscribers-{$index}.csv", $subscribers);
            });

        $this->addMeta('subscribers_count', $subscribersCount);
    }
}
