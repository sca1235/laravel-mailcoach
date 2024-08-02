<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Actions\DuplicateCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class CampaignsComponent extends TableComponent
{
    public function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();
        $campaignsTable = self::getCampaignTableName();

        return self::getCampaignClass()::query()
            ->select(self::getCampaignTableName().'.*')
            ->with(['emailList', 'contentItems'])
            ->addSelect(
                DB::raw(Mailcoach::isPostgresqlDatabase()
                ? <<<"SQL"
                    CASE
                        WHEN status = 'draft' AND scheduled_at IS NULL THEN '2999-01-01'::timestamp + INTERVAL '1 day' * {$prefix}{$campaignsTable}.id
                        WHEN {$prefix}{$campaignsTable}.scheduled_at IS NOT NULL THEN {$prefix}{$campaignsTable}.scheduled_at
                        WHEN {$prefix}{$campaignsTable}.sent_at IS NOT NULL THEN {$prefix}{$campaignsTable}.sent_at
                        ELSE {$prefix}{$campaignsTable}.updated_at
                    END as sent_sort
                SQL
                : <<<"SQL"
                    CASE
                        WHEN status = 'draft' AND scheduled_at IS NULL THEN CONCAT(999999999, {$prefix}{$campaignsTable}.id)
                        WHEN {$prefix}{$campaignsTable}.scheduled_at IS NOT NULL THEN {$prefix}{$campaignsTable}.scheduled_at
                        WHEN {$prefix}{$campaignsTable}.sent_at IS NOT NULL THEN {$prefix}{$campaignsTable}.sent_at
                        ELSE {$prefix}{$campaignsTable}.updated_at
                    END as 'sent_sort'
                SQL
                )
            );
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTableGroupingDirection(): ?string
    {
        return $this->getTableSortDirection() ?? 'desc';
    }

    public function getTableGrouping(): ?Group
    {
        return Group::make('sent_sort')
            ->getTitleFromRecordUsing(function ($record) {
                return match (true) {
                    $record->status === CampaignStatus::Sending => __mc('Sending'),
                    $record->status === CampaignStatus::Draft && $record->scheduled_at => __mc('Scheduled'),
                    $record->status === CampaignStatus::Draft && ! $record->scheduled_at => __mc('Drafts'),
                    $record->status === CampaignStatus::Sent || $record->status === CampaignStatus::Cancelled => __mc('Sent campaigns'),
                    default => '',
                };
            })
            ->label('');
    }

    public function getTable(): Table
    {
        return parent::getTable()
            ->poll(function () {
                if ($this->getTableRecords()->where('status', CampaignStatus::Sending)->count() > 0) {
                    return '10s';
                }

                return null;
            });
    }

    protected function getTableColumns(): array
    {
        $searchable = self::getCampaignClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];

        return [
            IconColumn::make('Status')
                ->label('')
                ->extraAttributes([
                    'class' => 'px-1 py-2',
                ])
                ->getStateUsing(fn (Campaign $record) => $record->status->value)
                ->icon(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => 'heroicon-s-clock',
                    $record->status === CampaignStatus::Draft => '',
                    $record->status === CampaignStatus::Sent => 'heroicon-s-check-circle',
                    $record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner() => 'heroicon-s-pause',
                    $record->status === CampaignStatus::Sending => 'heroicon-s-arrow-path',
                    $record->status === CampaignStatus::Cancelled => 'heroicon-s-x-circle',
                    default => '',
                })
                ->extraAttributes(function (Campaign $record) {
                    if ($record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner()) {
                        return [];
                    }

                    if ($record->status === CampaignStatus::Sending) {
                        return ['class' => 'animate-spin'];
                    }

                    return [];
                })
                ->tooltip(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => __mc('Scheduled'),
                    $record->status === CampaignStatus::Sent => __mc('Sent'),
                    $record->status === CampaignStatus::Draft => __mc('Draft'),
                    $record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner() => __mc('Awaiting split results'),
                    $record->status === CampaignStatus::Sending => __mc('Sending'),
                    $record->status === CampaignStatus::Cancelled => __mc('Cancelled'),
                    default => '',
                })
                ->color(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => Color::hex('#648BEF'),
                    $record->status === CampaignStatus::Draft => '',
                    $record->status === CampaignStatus::Sent => Color::hex('#0FBA9E'),
                    $record->status === CampaignStatus::Sending => Color::hex('#648BEF'),
                    $record->status === CampaignStatus::Cancelled => 'danger',
                    default => '',
                })
                ->alignCenter(),
            TextColumn::make('name')
                ->searchable(
                    condition: $searchable,
                    query: function (Builder $query, $search) {
                        return $query->where(self::getCampaignTableName().'.name', 'like', "%{$search}%");
                    }
                )
                ->view('mailcoach::app.campaigns.columns.name'),
            TextColumn::make('List')
                ->url(fn (Campaign $record) => $record->emailList
                    ? route('mailcoach.emailLists.summary', $record->emailList)
                    : null
                )
                ->view('mailcoach::app.campaigns.columns.email_list'),
            TextColumn::make('emails')
                ->label(__mc('Emails'))
                ->alignRight()
                ->numeric()
                ->view('mailcoach::app.campaigns.columns.sends'),
            TextColumn::make('unique_open_count')
                ->label(__mc('Opens'))
                ->alignRight()
                ->numeric()
                ->view('mailcoach::app.campaigns.columns.opens'),
            TextColumn::make('unique_click_count')
                ->alignRight()
                ->numeric()
                ->label(__mc('Clicks'))
                ->view('mailcoach::app.campaigns.columns.clicks'),
            TextColumn::make('sent_sort')
                ->label(__mc('Sent'))
                ->alignRight()
                ->sortable()
                ->view('mailcoach::app.campaigns.columns.sent'),
        ];
    }

    protected function getTableFilters(): array
    {
        if (self::getCampaignClass()::count() <= $this->getTableRecordsPerPageSelectOptions()[0]) {
            return [];
        }

        return [
            SelectFilter::make('status')
                ->options([
                    'sent' => 'Sent',
                    'scheduled' => 'Scheduled',
                    'sending' => 'Sending',
                    'draft' => 'Draft',
                ])
                ->query(function (Builder $query, array $data) {
                    return match ($data['value']) {
                        'sent' => $query->sent(),
                        'scheduled' => $query->scheduled(),
                        'sending' => $query->sending(),
                        'draft' => $query->draft(),
                        default => $query,
                    };
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('unschedule')
                    ->action(function (Campaign $record) {
                        $record->markAsUnscheduled();
                        notify(__mc('Campaign :campaign unscheduled.', ['campaign' => $record->name]));
                    })
                    ->icon('heroicon-s-stop')
                    ->hidden(fn (Campaign $record) => ! $record->isScheduled())
                    ->label(__mc('Unschedule')),
                Action::make('Duplicate')
                    ->action(function (Campaign $record) {
                        $this->duplicateCampaign($record);
                    })
                    ->icon('heroicon-s-document-duplicate')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(function (Campaign $record) {
                        $record->delete();
                        notify(__mc('Campaign :campaign was deleted.', ['campaign' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger'),
            ]),
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
                ->action(function (Collection $records) {
                    $records->each->delete();
                    notify(__mc('Campaigns successfully deleted.'));
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Campaign $record) {
            if ($record->isSent() || $record->isSending() || $record->isCancelled()) {
                return route('mailcoach.campaigns.summary', $record);
            }

            if ($record->isScheduled()) {
                return route('mailcoach.campaigns.delivery', $record);
            }

            return route('mailcoach.campaigns.content', $record);
        };
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No campaigns yet');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (! self::getEmailListClass()::count()) {
            return __mc('You‘ll need a list first');
        }

        return __mc('Go write something!');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('create_list')
                ->url(route('mailcoach.emailLists').'#create-list')
                ->label(__mc('New email list'))
                ->link()
                ->hidden(self::getEmailListClass()::count() > 0),
            Action::make('learn')
                ->url('https://mailcoach.app/resources/learn-mailcoach/features/campaigns')
                ->label(__mc('Learn more about campaigns'))
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    public function duplicateCampaign(Campaign $campaign): void
    {
        $this->authorize('create', self::getCampaignClass());

        $duplicateCampaign = app(DuplicateCampaignAction::class)->execute($campaign);

        notify(__mc('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        $this->redirect(route('mailcoach.campaigns.settings', $duplicateCampaign));
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Campaigns'),
            'create' => Auth::guard(config('mailcoach.guard'))->user()->can('create', self::getCampaignClass())
                ? 'campaign'
                : null,
            'hideBreadcrumbs' => true,
        ];
    }
}
