<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Content\Models\Open;

class OpensComponent extends ContentItemTable
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'first_opened_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();
        $openTableName = static::getOpenTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();

        return self::getOpenClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as id,
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$openTableName}.subscriber_id) as open_count,
                min({$prefix}{$openTableName}.created_at) AS first_opened_at
            ")
            ->join(static::getContentItemTableName(), static::getContentItemTableName().'.id', '=', "{$openTableName}.content_item_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$openTableName}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->whereIn(static::getContentItemTableName().'.id', $this->contentItems->pluck('id'))
            ->groupBy("{$subscriberTableName}.uuid", "{$emailListTableName}.uuid", "{$subscriberTableName}.email");
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->subscriber_uuid;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return self::getOpenClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function getTableColumns(): array
    {
        $prefix = DB::getTablePrefix();
        $subscriberTableName = static::getSubscriberTableName();

        return [
            TextColumn::make("{$prefix}{$subscriberTableName}.email")
                ->getStateUsing(fn ($record) => $record->subscriber_email)
                ->label(__mc('Email'))
                ->sortable()
                ->searchable(query: function (Builder $query, string $search) {
                    $query->where(self::getSubscriberTableName().'.email', 'like', "%{$search}%");
                })
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('open_count')
                ->label(__mc('Opens'))
                ->sortable()
                ->numeric()
                ->alignRight(),
            TextColumn::make('first_opened_at')
                ->label(__mc('First opened at'))
                ->getStateUsing(fn ($record) => Carbon::parse($record->first_opened_at)->toMailcoachFormat())
                ->alignRight()
                ->sortable(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            return route('mailcoach.emailLists.subscriber.details', [$record->subscriber_email_list_uuid, $record->subscriber_uuid]);
        };
    }

    protected function getTableBulkActions(): array
    {
        $prefix = DB::getTablePrefix();
        $subscriberTableName = static::getSubscriberTableName();

        return [
            BulkAction::make('export')
                ->label(__mc('Export selected'))
                ->icon('heroicon-s-cloud-arrow-down')
                ->action(function () use ($prefix, $subscriberTableName) {
                    $header = [
                        'email',
                        'opens',
                        'first_opened_at',
                    ];

                    $rows = $this->getTableQuery()->whereIn("{$prefix}{$subscriberTableName}.uuid", $this->selectedTableRecords)->get();

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (Open $row) {
                            return [
                                'email' => $row->subscriber_email,
                                'opens' => $row->open_count,
                                'first_opened_at' => $row->first_opened_at->toMailcoachFormat(),
                            ];
                        },
                        title: $this->getTitle(),
                    );
                }),
        ];
    }

    public function getTitle(): string
    {
        return __mc('Opens');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope-open';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (method_exists($this->model, 'tracking')) {
            [$openTracking, $clickTracking] = $this->model->tracking();

            if ($openTracking || $clickTracking) {
                return __mc('No opens yet. Stay tuned.');
            }
        }

        return __mc('No opens tracked.');
    }
}
