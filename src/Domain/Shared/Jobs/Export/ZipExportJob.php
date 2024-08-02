<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Composer\InstalledVersions;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class ZipExportJob extends ExportJob
{
    public function __construct(protected string $path)
    {
    }

    public function name(): string
    {
        return 'Zip Export';
    }

    public function execute(): void
    {
        $this->addMeta('version', InstalledVersions::getVersion('spatie/laravel-mailcoach'));

        $files = $this->exportDisk->files($this->path);
        foreach ($files as $file) {
            $this->tmpDisk->put($file, $this->exportDisk->get($file));
        }

        $zipFilePath = $this->path.DIRECTORY_SEPARATOR.'mailcoach-export.zip';

        $zipFile = new ZipArchive();
        $zipFile->open($this->tmpDisk->path($zipFilePath), ZipArchive::CREATE);

        $files = Finder::create()
            ->in($this->tmpDisk->path($this->path))
            ->files();

        foreach ($files as $file) {
            if (! in_array($file->getExtension(), ['csv', 'json'])) {
                continue;
            }

            $zipFile->addFile($file->getPathname(), $file->getFilename());
        }

        $zipFile->close();

        $this->exportDisk->put($zipFilePath, $this->tmpDisk->get($zipFilePath));
    }
}
