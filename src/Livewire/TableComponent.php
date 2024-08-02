<?php

namespace Spatie\Mailcoach\Livewire;

use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class TableComponent extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use UsesMailcoachModels;

    #[Url]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $tableFilters = null;

    #[Url]
    public ?string $tableGrouping = null;

    #[Url]
    public ?string $tableGroupingDirection = null;

    /**
     * @var ?string
     */
    #[Url]
    public $tableSearch = '';

    #[Url]
    public ?string $tableSortColumn = null;

    #[Url]
    public ?string $tableSortDirection = null;

    abstract protected function getTableQuery(): Builder;

    protected function isTablePaginationEnabled(): bool
    {
        return $this->getTableQuery()->count() > $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return [10];
    }

    public function getView(): View
    {
        return view('mailcoach::app.table');
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.app';
    }

    public function getLayoutData(): array
    {
        return [];
    }

    public function __construct()
    {
        FilamentView::spa(true);
    }

    public function export(array $header, Collection $rows, Closure $formatRow, string $title = ''): StreamedResponse
    {
        ini_set('max_execution_time', '0');

        $filename = trim("{$title} export.csv");

        return response()->streamDownload(function () use ($header, $filename, $rows, $formatRow) {
            $csv = SimpleExcelWriter::streamDownload($filename);

            $csv->addHeader($header);

            $rows->each(function ($row) use ($csv, $formatRow) {
                $csv->addRow($formatRow($row));

                flush();
            });

            $csv->close();
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return $this->getView()->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
        ], $this->getLayoutData()));
    }
}
