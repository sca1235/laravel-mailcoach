<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Livewire\TableComponent;

class SubscriberSendsComponent extends TableComponent
{
    public EmailList $emailList;

    public Subscriber $subscriber;

    public function mount(EmailList $emailList, Subscriber $subscriber)
    {
        $this->emailList = $emailList;
        $this->subscriber = $subscriber;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTitle(): string
    {
        if ($this->subscriber->first_name || $this->subscriber->last_name) {
            return trim("{$this->subscriber->first_name} {$this->subscriber->last_name}");
        }

        return $this->subscriber->email;
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'subscriber' => $this->subscriber,
            'totalSendsCount' => self::getSendClass()::query()->where('subscriber_id', $this->subscriber->id)->count(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSendClass()::query()
            ->with([
                'contentItem' => function (Builder $query) {
                    $query->with('model');
                },
            ])
            ->withCount(['opens', 'clicks'])
            ->where('subscriber_id', $this->subscriber->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('contentItem.model.name')
                ->label(__mc('Name'))
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('opens_count')
                ->label(__mc('Opens'))
                ->numeric()
                ->alignRight()
                ->sortable(),
            TextColumn::make('clicks_count')
                ->label(__mc('Clicks'))
                ->numeric()
                ->alignRight()
                ->sortable(),
            TextColumn::make('sent_at')
                ->label(__mc('Sent at'))
                ->dateTime(config('mailcoach.date_format'))
                ->alignRight()
                ->sortable(),
        ];
    }

    public function getTableGrouping(): ?Group
    {
        return Group::make('contentItem.model_type')
            ->getTitleFromRecordUsing(fn (Send $record) => match ($record->contentItem->model_type) {
                'campaign' => __mc('Campaigns'),
                'automation_mail' => __mc('Automation mails'),
                default => '',
            })
            ->label('');
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->label(__mc('Type'))
                ->options([
                    'campaign' => __mc('Campaigns'),
                    'automation' => __mc('Automation mails'),
                ])
                ->query(function (Builder $query, $data) {
                    return match ($data['value']) {
                        'campaign' => $query->whereNotNull('campaign_id'),
                        'automation' => $query->whereNotNull('automation_mail_id'),
                        default => $query,
                    };
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Send $record) => match (true) {
            $record->contentItem->model instanceof Campaign => route('mailcoach.campaigns.summary', $record->contentItem->model),
            $record->contentItem->model instanceof AutomationMail => route('mailcoach.automations.mails.summary', $record->contentItem->model),
            default => '',
        };
    }

    public function retry(int $sendId): void
    {
        $send = self::getSendClass()::findOrFail($sendId);

        $this->authorize('view', $send->subscriber->emailList);

        $send->prepareRetryAfterFailedSend();

        dispatch(new SendCampaignMailJob($send));

        notify(__mc('Retrying to send :failedSendsCount mails...', ['failedSendsCount' => 1]));
    }
}
