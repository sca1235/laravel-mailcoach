<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberExportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class SubscriberExportsComponent extends TableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists'));
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function downloadFile(SubscriberExport $subscriberExport)
    {
        abort_unless((bool) $subscriberExport->getMediaCollection('file'), 403);

        return $subscriberExport->getFirstMedia('file');
    }

    public function deleteExport(SubscriberExport $export)
    {
        $this->authorize('delete', $export);

        $export->delete();

        notify(__mc('Export was deleted.'));
    }

    public function restartExport(SubscriberExport $export): void
    {
        $export->update([
            'status' => SubscriberExportStatus::Pending,
            'errors' => [],
        ]);

        dispatch(new ExportSubscribersJob($export, Auth::guard(config('mailcoach.guard'))->user()));

        notify(__mc('Export successfully restarted.'));
    }

    public function getTitle(): string
    {
        return __mc('Subscriber exports');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSubscriberExportClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->with('emailList');
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->icon(fn (SubscriberExport $record) => match (true) {
                    $record->status === SubscriberExportStatus::Exporting => 'heroicon-s-arrow-path',
                    $record->status === SubscriberExportStatus::Pending => 'heroicon-s-clock',
                    $record->status === SubscriberExportStatus::Completed => 'heroicon-s-check-circle',
                    $record->status === SubscriberExportStatus::Failed => 'heroicon-s-exclamation-circle',
                })
                ->tooltip(fn (SubscriberExport $record) => match (true) {
                    $record->status === SubscriberExportStatus::Exporting => __mc('Exporting'),
                    $record->status === SubscriberExportStatus::Pending => __mc('Pending'),
                    $record->status === SubscriberExportStatus::Completed => __mc('Completed'),
                    $record->status === SubscriberExportStatus::Failed => __mc('Failed'),
                })
                ->color(fn (SubscriberExport $record) => match (true) {
                    $record->status === SubscriberExportStatus::Exporting => 'warning',
                    $record->status === SubscriberExportStatus::Pending => 'warning',
                    $record->status === SubscriberExportStatus::Completed => 'success',
                    $record->status === SubscriberExportStatus::Failed => 'danger',
                })
                ->extraAttributes(fn (SubscriberExport $record) => match (true) {
                    $record->status === SubscriberExportStatus::Exporting => ['class' => 'animate-spin'],
                    $record->status === SubscriberExportStatus::Pending => ['class' => 'animate-spin'],
                    default => [],
                })
                ->sortable(),
            TextColumn::make('created_at')
                ->label(__mc('Started at'))
                ->sortable()
                ->dateTime(config('mailcoach.date_format')),
            TextColumn::make('exported_subscribers_count')
                ->sortable()
                ->label(__mc('Exported subscribers'))
                ->numeric(),
            TextColumn::make('errors')
                ->getStateUsing(fn (SubscriberExport $record) => implode('<br/>', $record->errors ?? []))
                ->html()
                ->numeric()
                ->label(__mc('Errors')),
        ];
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Create an export from the subscribers overview.');
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download-file')
                    ->label(__mc('Download export'))
                    ->icon('heroicon-s-document-text')
                    ->action(fn (SubscriberExport $record) => $this->downloadFile($record))
                    ->hidden(fn (SubscriberExport $record) => ! $record->hasMedia('file')),
                Action::make('restart')
                    ->label(__mc('Restart'))
                    ->requiresConfirmation()
                    ->icon('heroicon-s-arrow-path')
                    ->action(fn (SubscriberExport $record) => $this->restartExport($record)),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (SubscriberExport $record) => $this->deleteExport($record))
                    ->authorize('delete', self::getSubscriberExportClass()),
            ]),
        ];
    }
}
