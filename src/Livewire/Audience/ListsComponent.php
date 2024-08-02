<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Livewire\TableComponent;

class ListsComponent extends TableComponent
{
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->sortable()
                ->searchable(self::getEmailListClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0])
                ->view('mailcoach::app.emailLists.columns.name'),
            TextColumn::make('from')
                ->label(__mc('From'))
                ->html()
                ->getStateUsing(fn (EmailList $record) => <<<"html"
                    {$record->default_from_name} <span class="text-xs text-navy-bleak-extra-light">{$record->default_from_email}</span>
                html),
            TextColumn::make('reply_to')
                ->label(__mc('Reply to'))
                ->html()
                ->getStateUsing(fn (EmailList $record) => <<<"html"
                    {$record->default_reply_to_name} <span class="text-xs text-navy-bleak-extra-light">{$record->default_reply_to_email}</span>
                html),
            TextColumn::make('active_subscribers_count')
                ->label(__mc('Subscribers'))
                ->sortable()
                ->numeric()
                ->alignRight()
                /** @phpstan-ignore-next-line The query adds this field */
                ->getStateUsing(fn (EmailList $record) => Str::shortNumber($record->active_subscribers_count)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Delete')
                ->action(function (EmailList $record) {
                    $this->authorize('delete', $record);

                    $record->delete();

                    notify(__mc('List :list was deleted.', ['list' => $record->name]));
                })
                ->requiresConfirmation()
                ->modalHeading(__mc('Delete list'))
                ->modalDescription(function (EmailList $record) {
                    return __mc('Are you sure you want to delete list :list?', ['list' => $record->name]);
                })
                ->label(' ')
                ->icon('heroicon-s-trash')
                ->color('danger'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (EmailList $record) {
            return route('mailcoach.emailLists.summary', $record);
        };
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    public function mount(): void
    {
        $this->authorize('viewAny', static::getEmailListClass());
    }

    protected function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();

        return self::getEmailListClass()::query()
            ->select(self::getEmailListTableName().'.*')
            ->selectSub(
                query: self::getSubscriberClass()::query()
                    ->subscribed()
                    ->where('email_list_id', DB::raw($prefix.self::getEmailListTableName().'.id'))
                    ->select(DB::raw('count(*)')),
                as: 'active_subscribers_count'
            );
    }

    public function getTitle(): string
    {
        return __mc('Lists');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No lists');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-user-group';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('learn')
                ->url('https://mailcoach.app/resources/learn-mailcoach/features/email-lists')
                ->label(__mc('Learn more about email lists'))
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    protected function getTableEmptyStateDescription(): string
    {
        return __mc('You\'ll need at least one list to gather subscribers.');
    }

    public function getLayoutData(): array
    {
        if (! Auth::guard(config('mailcoach.guard'))->user()->can('create', self::getEmailListClass())) {
            return [
                'hideBreadcrumbs' => true,
            ];
        }

        return [
            'create' => 'list',
            'hideBreadcrumbs' => true,
        ];
    }
}
