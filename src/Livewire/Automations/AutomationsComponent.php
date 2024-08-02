<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Automation\Actions\DuplicateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class AutomationsComponent extends TableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getAutomationClass()::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('status')
                ->label(__mc('Status'))
                ->sortable()
                ->width(0)
                ->view('mailcoach::app.automations.columns.status'),
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->sortable()
                ->searchable(self::getAutomationClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0])
                ->size('base')
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('List')
                ->sortable(query: function (\Illuminate\Database\Eloquent\Builder $query, $direction) {
                    $query->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getAutomationTableName().'.email_list_id')
                        ->orderBy(self::getEmailListTableName().'.name', $direction);
                })
                ->url(fn (Automation $record) => $record->emailList
                    ? route('mailcoach.emailLists.summary', $record->emailList)
                    : null
                )
                ->view('mailcoach::app.automations.columns.email_list'),
            TextColumn::make('updated_at')
                ->label(__mc('Updated'))
                ->alignRight()
                ->sortable()
                ->size('sm')
                ->width(0)
                ->extraAttributes([
                    'class' => 'tabular-nums',
                ])
                ->date(config('mailcoach.date_format'), config('mailcoach.timezone')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Start')
                    ->label(__mc('Start'))
                    ->icon('heroicon-s-play')
                    ->action(function (Automation $record) {
                        $record->start();
                        notify(__mc('Automation :automation started.', ['automation' => $record->name]));
                    })
                    ->hidden(fn (Automation $record) => $record->status === AutomationStatus::Started || ! $record->getTrigger()),
                Action::make('Pause')
                    ->label(__mc('Pause'))
                    ->icon('heroicon-s-pause')
                    ->action(function (Automation $record) {
                        $record->pause();
                        notify(__mc('Automation :automation paused.', ['automation' => $record->name]));
                    })
                    ->hidden(fn (Automation $record) => $record->status === AutomationStatus::Paused),
                Action::make('Duplicate')
                    ->action(fn (Automation $record) => $this->duplicateAutomation($record))
                    ->icon('heroicon-s-document-duplicate')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(function (Automation $record) {
                        $record->delete();
                        notify(__mc('Automation :automation was deleted.', ['automation' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger'),
            ]),
        ];
    }

    protected function getTableFilters(): array
    {
        if (self::getAutomationClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0]) {
            return [
                SelectFilter::make('status')
                    ->options([
                        'started' => __mc('Running'),
                        'paused' => __mc('Paused'),
                    ]),
            ];
        }

        return [];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Automation $record) {
            return route('mailcoach.automations.settings', $record);
        };
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'automations';
    }

    public function toggleAutomationStatus(int $id)
    {
        $automation = self::getAutomationClass()::findOrFail($id);

        $automation->update([
            'status' => $automation->status === AutomationStatus::Paused
                ? AutomationStatus::Started
                : AutomationStatus::Paused,
        ]);

        $this->dispatch('notify', [
            'content' => __mc('Automation :automation was :status.', ['automation' => $automation->name, 'status' => $automation->status->value]),
        ]);
    }

    public function duplicateAutomation(Automation $automation)
    {
        /** @var DuplicateAutomationAction $action */
        $action = Mailcoach::getAutomationActionClass('duplicate_automation', DuplicateAutomationAction::class);
        $duplicateAutomation = $action->execute($automation);

        notify(__mc('Automation :automation was created.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }

    public function getTitle(): string
    {
        return __mc('Automations');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No automations');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-queue-list';
    }

    protected function getTableEmptyStateDescription(): string
    {
        return __mc('A good automation to start with is a welcome email.');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('learn')
                ->url('https://mailcoach.app/resources/learn-mailcoach/features/automations')
                ->label(__mc('Learn more about automations'))
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    public function getLayoutData(): array
    {
        return [
            'create' => Auth::guard(config('mailcoach.guard'))->user()->can('create', self::getAutomationClass())
                ? 'automation'
                : null,
            'hideBreadcrumbs' => true,
        ];
    }
}
