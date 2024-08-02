<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMailsComponent extends TableComponent
{
    public function mount()
    {
        $this->authorize('viewAny', static::getTransactionalMailClass());
    }

    protected function getTableQuery(): Builder
    {
        return self::getTransactionalMailClass()::query();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableColumns(): array
    {
        $searchEnabled = self::getTransactionalMailClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];

        return [
            TextColumn::make('name')
                ->sortable()
                ->label(__mc('Name'))
                ->extraAttributes(['class' => 'link'])
                ->searchable($searchEnabled),
            TextColumn::make('contentItem.subject')
                ->label(__mc('Subject'))
                ->searchable($searchEnabled),
            TextColumn::make('to')
                ->sortable()
                ->label(__mc('To'))
                ->searchable(Mailcoach::isPostgresqlDatabase() ? '"to"' : $searchEnabled),
            TextColumn::make('unique_open_count')
                ->label(__mc('Opens'))
                ->alignRight()
                ->numeric()
                ->view('mailcoach::app.transactionalMails.templates.columns.opens'),
            TextColumn::make('unique_click_count')
                ->alignRight()
                ->numeric()
                ->label(__mc('Clicks'))
                ->view('mailcoach::app.transactionalMails.templates.columns.clicks'),
            IconColumn::make('store_mail')
                ->label(__mc('Store'))
                ->alignCenter()
                ->width(0)
                ->icons([
                    'heroicon-s-check-circle' => true,
                    'heroicon-s-x-circle' => false,
                ])
                ->tooltip(fn (TransactionalMail $record) => match ($record->store_mail) {
                    true => __mc('Store in log when sending'),
                    false => __mc('Don\'t store in log when sending'),
                })
                ->color(fn (TransactionalMail $record) => match ($record->store_mail) {
                    true => 'success',
                    false => 'gray',
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (TransactionalMail $record) {
            if ($record->openCount() || $record->clickCount()) {
                return route('mailcoach.transactionalMails.templates.summary', $record);
            }

            return route('mailcoach.transactionalMails.templates.edit', $record);
        };
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (TransactionalMail $record) => $this->duplicateTransactionalMail($record))
                    ->icon('heroicon-s-document-duplicate')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(function (TransactionalMail $record) {
                        $record->delete();
                        notify(__mc('Transactional email :transactionalMail was deleted.', ['transactionalMail' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger'),
            ]),
        ];
    }

    public function duplicateTransactionalMail(TransactionalMail $transactionalMail)
    {
        $this->authorize('create', self::getTransactionalMailClass());

        /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $duplicateTemplate */
        $duplicateTemplate = self::getTransactionalMailClass()::create([
            'uuid' => Str::uuid(),
            'name' => $transactionalMail->name.'-copy',
            'from' => $transactionalMail->from,
            'cc' => $transactionalMail->cc,
            'to' => $transactionalMail->to,
            'bcc' => $transactionalMail->bcc,
            'type' => $transactionalMail->type,
            'replacers' => $transactionalMail->replacers,
            'store_mail' => $transactionalMail->store_mail,
            'test_using_mailable' => $transactionalMail->test_using_mailable,
        ]);

        $duplicateTemplate->contentItem->update([
            'subject' => $transactionalMail->contentItem->subject,
            'template_id' => $transactionalMail->contentItem->template_id,
            'html' => $transactionalMail->contentItem->html,
            'structured_html' => $transactionalMail->contentItem->structured_html,
            'utm_tags' => (bool) $transactionalMail->contentItem->utm_tags,
        ]);

        notify(__mc('Email :name was created.', ['name' => $transactionalMail->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No transactional emails');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Create transactional emails in the Mailcoach UI that can be sent in your application');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('learn')
                ->url('https://mailcoach.app/resources/learn-mailcoach/features/transactional')
                ->label(__mc('Learn more about transactional emails'))
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    public function getLayoutData(): array
    {
        if (Auth::guard(config('mailcoach.guard'))->user()->can('create', self::getTransactionalMailClass())) {
            return ['create' => 'transactional-template', 'createText' => __mc('Create email')];
        }

        return [];
    }
}
