<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMailLogItemsComponent extends TableComponent
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        return self::getTransactionalMailLogItemClass()::query()
            ->with(['contentItem' => function (Builder $query) {
                $query->withCount(['opens', 'clicks']);
            }]);
    }

    protected function getTableColumns(): array
    {
        $searchable = self::getTransactionalMailLogItemClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];

        return [
            IconColumn::make('fake')
                ->label('')
                ->icon(fn (TransactionalMailLogItem $record) => $record->fake ? 'heroicon-s-command-line' : 'heroicon-s-envelope')
                ->tooltip(fn (TransactionalMailLogItem $record) => $record->fake ? __mc('Fake send') : __mc('Sent'))
                ->color(fn (TransactionalMailLogItem $record) => $record->fake ? 'primary' : 'success'),
            TextColumn::make('mail_name')
                ->label(__mc('Email')),
            TextColumn::make('contentItem.subject')
                ->extraAttributes(['class' => 'link'])
                ->size('base')
                ->label(__mc('Subject'))
                ->searchable($searchable),
            TextColumn::make('to')
                ->size('base')
                ->getStateUsing(fn (TransactionalMailLogItem $record) => $record->toString())
                ->searchable(Mailcoach::isPostgresqlDatabase() ? '"to"' : $searchable),
            TextColumn::make('contentItem.opens_count')->size('sm')->width(0)->alignRight()->label(__mc('Opens'))->numeric(),
            TextColumn::make('contentItem.clicks_count')->size('sm')->width(0)->alignRight()->label(__mc('Clicks'))->numeric(),
            TextColumn::make('created_at')
                ->alignRight()
                ->width(0)
                ->label(__mc('Sent'))
                ->sortable()
                ->size('base')
                ->extraAttributes([
                    'class' => 'tabular-nums',
                ])
                ->date(config('mailcoach.date_format'), config('mailcoach.timezone')),
        ];
    }

    protected function getTableFilters(): array
    {
        $searchable = self::getTransactionalMailLogItemClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];

        if (! $searchable) {
            return [];
        }

        return [
            SelectFilter::make('mail_name')
                ->label(__mc('Mail name'))
                ->options(self::getTransactionalMailClass()::pluck('name', 'name')),
            Filter::make('created_at')
                ->form([
                    DateTimePicker::make('from')->label(__mc('From')),
                    DateTimePicker::make('until')->label(__mc('Until')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['from'] ?? null) {
                        $indicators['from'] = __mc('Created from :date', ['date' => Carbon::parse($data['from'])->toMailcoachFormat()]);
                    }

                    if ($data['until'] ?? null) {
                        $indicators['until'] = __mc('Created until :date', ['date' => Carbon::parse($data['until'])->toMailcoachFormat()]);
                    }

                    return $indicators;
                })
                ->label(__mc('Sent at')),
            Filter::make('opens')
                ->label(__mc('Has opens'))
                ->query(fn (Builder $query) => $query->whereRelation('contentItem', 'open_count', '>', 0))
                ->toggle(),
            Filter::make('clicks')
                ->label(__mc('Has clicks'))
                ->query(fn (Builder $query) => $query->whereRelation('contentItem', 'click_count', '>', 0))
                ->toggle(),
            Filter::make('fake')
                ->query(fn (Builder $query) => $query->where('fake', true))
                ->toggle(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Delete')
                ->action(function (TransactionalMailLogItem $record) {
                    $record->delete();
                    notify(__mc('Log was deleted.'));
                })
                ->requiresConfirmation()
                ->label(' ')
                ->icon('heroicon-s-trash')
                ->color('danger'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->requiresConfirmation()
                ->icon('heroicon-s-trash')
                ->color('danger')
                ->deselectRecordsAfterCompletion()
                ->action(fn (Collection $records) => $records->each->delete()),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (TransactionalMailLogItem $record) => route('mailcoach.transactionalMails.show', $record);
    }

    public function getTitle(): string
    {
        return __mc('Log');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No transactional emails logged');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Transactional emails sent through Mailcoach will be logged here.');
    }
}
