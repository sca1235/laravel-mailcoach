<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSimpleExcelReaderAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportHasEmailHeaderAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class SubscriberImportsComponent extends TableComponent
{
    public EmailList $emailList;

    public string $replaceTags = 'append';

    public bool $subscribeUnsubscribed = false;

    public bool $unsubscribeMissing = false;

    public bool $showForm = true;

    public bool $sendNotification = true;

    public $file;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists'));

        $this->showForm = self::getSubscriberImportClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->count() === 0;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-document-plus';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Start a new subscriber import below.');
    }

    public function removeFile(): void
    {
        $this->file = null;
    }

    public function startImport(): void
    {
        $this->validate([
            'file' => ['file', 'mimes:txt,csv,xls,xlsx', 'max:5120'],
        ], [
            'file.max' => __mc('The uploaded file must not be greater than 5MB. We suggest splitting the import files into multiple smaller files.'),
        ]);

        $this->authorize('update', $this->emailList);

        /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file */
        $file = $this->file;

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport $subscriberImport */
        $subscriberImport = self::getSubscriberImportClass()::create([
            'email_list_id' => $this->emailList->id,
            'subscribe_unsubscribed' => $this->subscribeUnsubscribed,
            'unsubscribe_others' => $this->unsubscribeMissing,
            'replace_tags' => $this->replaceTags === 'replace',
        ]);

        $subscriberImport
            ->addMediaFromStream($file->readStream())
            ->setFileName($file->getClientOriginalName())
            ->toMediaCollection('importFile', config('mailcoach.import_disk'));

        $reader = app(CreateSimpleExcelReaderAction::class)->execute($subscriberImport);

        if (! resolve(ImportHasEmailHeaderAction::class)->execute($reader->getHeaders() ?? [])) {
            $subscriberImport->delete();
            $file->delete();
            $this->addError('file', __mc('No header row found. Make sure your first row has at least 1 column with "email"'));

            return;
        }

        $user = auth()->guard(config('mailcoach.guard'))->user();

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null, $this->sendNotification));

        notify(__mc('Your file has been uploaded. Follow the import status in the list below.'));

        $this->file = null;
        $this->showForm = false;
    }

    public function downloadAttachment(SubscriberImport $subscriberImport, string $collection)
    {
        if ($collection === 'errorReport' && ! is_numeric($subscriberImport->errors)) {
            $temporaryDirectory = TemporaryDirectory::make();

            app()->terminating(function () use ($temporaryDirectory) {
                $temporaryDirectory->delete();
            });

            return response()->download(
                SimpleExcelWriter::create($temporaryDirectory->path('errorReport.csv'), 'csv')
                    ->noHeaderRow()
                    ->addRows(json_decode($subscriberImport->errors ?? '[]', true))
                    ->getPath()
            );
        }

        abort_unless((bool) $subscriberImport->getMediaCollection($collection), 403);

        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport->id);

        return $subscriberImport->getFirstMedia($collection);
    }

    public function downloadExample()
    {
        $temporaryDirectory = TemporaryDirectory::make();

        app()->terminating(function () use ($temporaryDirectory) {
            $temporaryDirectory->delete();
        });

        return response()->download(
            SimpleExcelWriter::create($temporaryDirectory->path('subscribers-example.csv'))
                ->addRow(['email' => 'john@doe.com', 'first_name' => 'John', 'last_name' => 'Doe', 'tags' => 'one;two'])
                ->getPath()
        );
    }

    public function deleteImport(SubscriberImport $import)
    {
        $this->authorize('delete', $import);

        $import->delete();

        notify(__mc('Import was deleted.'));
    }

    public function restartImport(SubscriberImport $import): void
    {
        $import->update(['status' => SubscriberImportStatus::Pending]);

        dispatch(new ImportSubscribersJob($import, Auth::guard(config('mailcoach.guard'))->user()));

        notify(__mc('Import successfully restarted.'));
    }

    public function getTitle(): string
    {
        return __mc('Import subscribers');
    }

    public function getView(): View
    {
        return view('mailcoach::app.emailLists.subscribers.import');
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
        return self::getSubscriberImportClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->with('emailList');
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->icon(fn (SubscriberImport $record) => match (true) {
                    $record->status === SubscriberImportStatus::Importing => 'heroicon-s-arrow-path',
                    $record->status === SubscriberImportStatus::Pending => 'heroicon-s-clock',
                    $record->status === SubscriberImportStatus::Draft => 'heroicon-s-pencil-square',
                    $record->status === SubscriberImportStatus::Completed => 'heroicon-s-check-circle',
                    $record->status === SubscriberImportStatus::Failed => 'heroicon-s-exclamation-circle',
                })
                ->tooltip(fn (SubscriberImport $record) => match (true) {
                    $record->status === SubscriberImportStatus::Importing => __mc('Importing'),
                    $record->status === SubscriberImportStatus::Pending => __mc('Pending'),
                    $record->status === SubscriberImportStatus::Draft => __mc('Draft'),
                    $record->status === SubscriberImportStatus::Completed => __mc('Completed'),
                    $record->status === SubscriberImportStatus::Failed => __mc('Failed'),
                })
                ->color(fn (SubscriberImport $record) => match (true) {
                    $record->status === SubscriberImportStatus::Importing => 'warning',
                    $record->status === SubscriberImportStatus::Pending => 'warning',
                    $record->status === SubscriberImportStatus::Draft => '',
                    $record->status === SubscriberImportStatus::Completed => 'success',
                    $record->status === SubscriberImportStatus::Failed => 'danger',
                })
                ->extraAttributes(fn (SubscriberImport $record) => match (true) {
                    $record->status === SubscriberImportStatus::Importing => ['class' => 'animate-spin'],
                    default => [],
                })
                ->sortable(),
            TextColumn::make('created_at')
                ->label(__mc('Started at'))
                ->sortable()
                ->dateTime(config('mailcoach.date_format')),
            TextColumn::make('imported_subscribers_count')
                ->sortable()
                ->label(__mc('Processed rows'))
                ->numeric(),
            TextColumn::make('errors')
                ->getStateUsing(fn (SubscriberImport $record) => $record->errorCount())
                ->numeric()
                ->label(__mc('Errors')),
        ];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download-errors')
                    ->label(__mc('Error report'))
                    ->icon('heroicon-s-exclamation-circle')
                    ->hidden(fn (SubscriberImport $record) => $record->errorCount() === 0)
                    ->action(fn (SubscriberImport $record) => $this->downloadAttachment($record, 'errorReport')),
                Action::make('download-uploaded-file')
                    ->label(__mc('Uploaded file'))
                    ->icon('heroicon-s-document-text')
                    ->action(fn (SubscriberImport $record) => $this->downloadAttachment($record, 'importFile')),
                Action::make('restart')
                    ->label(__mc('Restart'))
                    ->requiresConfirmation()
                    ->icon('heroicon-s-arrow-path')
                    ->action(fn (SubscriberImport $record) => $this->restartImport($record)),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (SubscriberImport $record) => $this->deleteImport($record))
                    ->authorize('delete', self::getSubscriberImportClass()),
            ]),
        ];
    }
}
