<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Content\Models\Unsubscribe;

class UnsubscribesComponent extends ContentItemTable
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTitle(): string
    {
        return __mc('Unsubscribes');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-user-minus';
    }

    protected function getTableQuery(): Builder
    {
        return self::getUnsubscribeClass()::query()
            ->with('subscriber')
            ->whereIn('content_item_id', $this->contentItems->pluck('id'));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber.email')
                ->label(__mc('Email'))
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('subscriber.first_name')
                ->label(__mc('First name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('subscriber.last_name')
                ->label(__mc('Last name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('created_at')
                ->label(__mc('Date'))
                ->sortable()
                ->alignRight(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Unsubscribe $record) {
            return route('mailcoach.emailLists.subscriber.details', [$record->subscriber->emailList, $record->subscriber]);
        };
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('export')
                ->label(__mc('Export selected'))
                ->icon('heroicon-s-cloud-arrow-down')
                ->action(function (Collection $rows) {
                    $header = [
                        'email',
                        'first_name',
                        'last_name',
                        'unsubscribed_at',
                    ];

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (Unsubscribe $unsubscribe) {
                            return [
                                'email' => $unsubscribe->subscriber?->email ?? '<deleted subscriber>',
                                'first_name' => $unsubscribe->subscriber ? $unsubscribe->subscriber->first_name : '<deleted subscriber>',
                                'last_name' => $unsubscribe->subscriber ? $unsubscribe->subscriber->last_name : '<deleted subscriber>',
                                'unsubscribed_at' => $unsubscribe->created_at->toMailcoachFormat(),
                            ];
                        },
                        title: "{$this->model->name} unsubscribes",
                    );
                }),
        ];
    }
}
