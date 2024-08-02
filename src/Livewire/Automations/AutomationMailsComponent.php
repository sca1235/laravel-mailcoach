<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\Mailcoach\Domain\Automation\Models\Action as ActionModel;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class AutomationMailsComponent extends TableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getAutomationMailClass()::query();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable(self::getAutomationMailClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0])
                ->label(__mc('Name'))
                ->size('base')
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('contentItem.sent_to_number_of_subscribers')
                ->sortable()
                ->label(__mc('Emails'))
                ->width(0)
                ->numeric()
                ->size('base')
                ->getStateUsing(fn (AutomationMail $record) => number_format($record->contentItem->sent_to_number_of_subscribers) ?: '–'),
            TextColumn::make('contentItem.unique_open_count')
                ->sortable()
                ->label(__mc('Opens'))
                ->width(0)
                ->numeric()
                ->view('mailcoach::app.automations.mails.columns.opens'),
            TextColumn::make('contentItem.unique_click_count')
                ->sortable()
                ->label(__mc('Clicks'))
                ->width(0)
                ->numeric()
                ->view('mailcoach::app.automations.mails.columns.clicks'),
            TextColumn::make('created_at')
                ->alignRight()
                ->width(0)
                ->sortable()
                ->label(__mc('Created'))
                ->size('base')
                ->extraAttributes([
                    'class' => 'tabular-nums',
                ])
                ->date(config('mailcoach.date_format'), config('mailcoach.timezone')),
        ];
    }

    protected function getTableFilters(): array
    {
        if (self::getAutomationMailClass()::count() <= $this->getTableRecordsPerPageSelectOptions()[0]) {
            return [];
        }

        return [
            SelectFilter::make('automation_uuid')
                ->label(__mc('Automation'))
                ->options(fn () => self::getAutomationClass()::pluck('name', 'uuid'))
                ->multiple()
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return;
                    }

                    $class = self::getAutomationMailClass();
                    $shortname = (new ReflectionClass(new $class))->getShortName();

                    $automationMailIds = self::getAutomationActionClass()::query()
                        ->whereHas('automation', fn (Builder $query) => $query->whereIn('uuid', $data['values']))
                        ->whereRaw(
                            Mailcoach::isPostgresqlDatabase()
                                ? "ENCODE(DECODE(action, 'base64'), 'escape') LIKE '%$shortname%'"
                                : 'FROM_BASE64(action) like \'%'.$shortname.'%\''
                        )
                        ->get()
                        ->map(function (ActionModel $action) use ($shortname) {
                            /**
                             * We want to get any action that has an automation email
                             * referenced. Therefore, we need to parse serialized
                             * string of the action to get the model identifier.
                             */
                            $rawAction = base64_decode($action->getRawOriginal('action'));
                            $idPart = Str::after($rawAction, $shortname.'";s:2:"id";i:');
                            $id = Str::before($idPart, ';');

                            return (int) $id;
                        });

                    $query->whereIn('id', $automationMailIds);
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (AutomationMail $record) => $this->duplicateAutomationMail($record))
                    ->icon('heroicon-s-document-duplicate')
                    ->label(__mc('Duplicate'))
                    ->hidden(fn (AutomationMail $record) => ! Auth::guard(config('mailcoach.guard'))->user()->can('create', self::getAutomationMailClass())),
                Action::make('Delete')
                    ->action(function (AutomationMail $record) {
                        $record->delete();
                        notify(__mc('Automation email :automationMail was deleted.', ['automationMail' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->hidden(fn (AutomationMail $record) => ! Auth::guard(config('mailcoach.guard'))->user()->can('delete', self::getAutomationMailClass())),
            ]),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (AutomationMail $record) {
            return route('mailcoach.automations.mails.summary', $record);
        };
    }

    public function duplicateAutomationMail(AutomationMail $automationMail)
    {
        $this->authorize('create', $automationMail);

        /** @var AutomationMail $newAutomationMail */
        $newAutomationMail = self::getAutomationMailClass()::create([
            'name' => __mc('Duplicate of').' '.$automationMail->name,
        ]);

        $newAutomationMail->contentItem->update([
            'subject' => $automationMail->contentItem->subject,
            'template_id' => $automationMail->contentItem->template_id,
            'html' => $automationMail->contentItem->html,
            'structured_html' => $automationMail->contentItem->structured_html,
            'webview_html' => $automationMail->contentItem->webview_html,
            'utm_tags' => $automationMail->contentItem->utm_tags,
        ]);

        notify(__mc('Email :name was created.', ['name' => $newAutomationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $newAutomationMail);
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No emails');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('You haven\'t created any automation emails.');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope';
    }

    public function getLayoutData(): array
    {
        return [
            'create' => 'automation-mail',
            'createText' => __mc('Create automation mail'),
        ];
    }
}
