<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\TableComponent;

class SuppressionListComponent extends TableComponent
{
    use UsesMailcoachModels;

    public function getTableQuery(): Builder
    {
        return self::getSuppressionClass()::query();
    }

    public function getTitle(): string
    {
        return __mc('Suppressions');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No suppressed emails');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-no-symbol';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Smooth sailing! No suppressed emails found');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('docs')
                ->label(__mc('Learn more about suppressions'))
                ->url('https://mailcoach.app/resources/learn-mailcoach/advanced/suppressions')
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Suppressions'),
            'create' => 'suppression',
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('email')
                ->label(__mc('Email'))
                ->sortable()
                ->searchable(),
            TextColumn::make('reason')
                ->label(__mc('Reason'))
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Reactivate')
                ->icon('heroicon-s-check-circle')
                ->action(fn (Suppression $record) => $this->reactivate($record))
                ->requiresConfirmation()
                ->hidden(fn (Suppression $record) => $record->reason === SuppressionReason::spamComplaint)
                ->label(__mc('Reactivate')),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function reactivate(Suppression $suppression): void
    {
        $suppression->delete();

        notify("Reactivated `{$suppression->email}` successfully");
    }
}
