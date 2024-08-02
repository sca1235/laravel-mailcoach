<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Mailcoach;

class ExportTransactionalMailTemplatesJob extends ExportJob
{
    /**
     * @param  array<int>  $selectedTransactionalMailTemplates
     */
    public function __construct(protected string $path, protected array $selectedTransactionalMailTemplates)
    {
    }

    public function name(): string
    {
        return 'Transactional Mail Templates';
    }

    public function execute(): void
    {
        $templates = DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getTransactionalMailTableName())
            ->whereIn(self::getTransactionalMailTableName().'.id', $this->selectedTransactionalMailTemplates)
            ->join(self::getContentItemTableName(), self::getContentItemTableName().'.model_id', '=', self::getTransactionalMailTableName().'.id')
            ->where(self::getContentItemTableName().'.model_type', (new (self::getTransactionalMailClass()))->getMorphClass())
            ->select(self::getTransactionalMailTableName().'.*', self::getContentItemTableName().'.html', self::getContentItemTableName().'.subject')
            ->get()
            ->map(function (object $transactionalMail) {
                $transactionalMail->body = $transactionalMail->html;

                return $transactionalMail;
            });

        $this->writeFile('transactional_mail_templates.csv', $templates);
        $this->addMeta('transactional_mail_templates_count', $templates->count());
    }
}
