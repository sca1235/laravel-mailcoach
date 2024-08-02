<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Mailcoach;

class ExportAutomationMailsJob extends ExportJob
{
    /**
     * @param  array<int>  $selectedAutomationMails
     */
    public function __construct(protected string $path, protected array $selectedAutomationMails)
    {
    }

    public function name(): string
    {
        return 'Automation Mails';
    }

    public function execute(): void
    {
        $prefix = DB::getTablePrefix();

        $automationMails = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getAutomationMailTableName())
            ->whereIn(self::getAutomationMailTableName().'.id', $this->selectedAutomationMails)
            ->join(self::getContentItemTableName(), self::getContentItemTableName().'.model_id', '=', self::getAutomationMailTableName().'.id')
            ->where(self::getContentItemTableName().'.model_type', (new (self::getAutomationMailClass()))->getMorphClass())
            ->select(
                self::getContentItemTableName().'.*',
                DB::raw($prefix.self::getContentItemTableName().'.id as content_item_id'),
                DB::raw($prefix.self::getContentItemTableName().'.uuid as content_item_uuid'),
                self::getAutomationMailTableName().'.*',
            )
            ->get();

        $this->writeFile('automation_mails.csv', $automationMails);
        $this->addMeta('automation_mails_count', $automationMails->count());

        $automationMailLinks = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getLinkTableName())
            ->whereIn('content_item_id', $automationMails->pluck('content_item_id'))
            ->get();

        $this->writeFile('automation_mail_links.csv', $automationMailLinks);
        $this->addMeta('automation_mail_links_count', $automationMailLinks->count());
    }
}
