<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Spatie\Mailcoach\Mailcoach;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTransactionalMailTemplatesJob extends ImportJob
{
    public function name(): string
    {
        return 'Transactional Mail Templates';
    }

    public function execute(): void
    {
        if (! $this->importDisk->exists('import/transactional_mail_templates.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/transactional_mail_templates.csv', $this->importDisk->readStream('import/transactional_mail_templates.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/transactional_mail_templates.csv'));
        $columns = Schema::connection(Mailcoach::getDatabaseConnection())->getColumnListing(self::getTransactionalMailTableName());
        $contentItemColumns = Schema::connection(Mailcoach::getDatabaseConnection())->getColumnListing(self::getContentItemTableName());

        $total = $this->getMeta('transactional_mail_templates_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            $transactionalMail = self::getTransactionalMailClass()::firstOrCreate([
                'name' => $row['name'],
                'type' => $row['type'],
            ], array_filter(Arr::except(Arr::only($row, $columns), ['id'])));

            $contentAttributes = array_filter(Arr::except(Arr::only($row, $contentItemColumns), ['id']));
            $contentAttributes['html'] = $row['body'] ?? $row['html'] ?? '';

            $transactionalMail->contentItem->update($contentAttributes);

            $this->updateJobProgress($index, $total);
        }

        $this->tmpDisk->delete('tmp/transactional_mail_templates.csv');
    }
}
