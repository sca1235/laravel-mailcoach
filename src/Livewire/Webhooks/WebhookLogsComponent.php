<?php

namespace Spatie\Mailcoach\Livewire\Webhooks;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Settings\Actions\ResendWebhookCallAction;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class WebhookLogsComponent extends TableComponent
{
    public WebhookConfiguration $webhook;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => $this->webhook->name,
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getWebhookLogClass()::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->sortable()
                ->dateTime(config('mailcoach.date_format'))
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('status_code')
                ->sortable()
                ->label(__mc('Status code'))
                ->color(fn (WebhookLog $record) => match (true) {
                    $record->status_code >= 200 && $record->status_code < 300 => 'success',
                    default => '',
                }),
            TextColumn::make('event_type')
                ->label(__mc('Event type'))
                ->getStateUsing(fn (WebhookLog $record) => Str::remove('Event', $record->event_type))
                ->searchable(),
            TextColumn::make('attempt')
                ->label(__mc('Attempt'))
                ->default(__mc('Manual'))
                ->numeric(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('resend')
                ->label(__mc('Resend'))
                ->action(fn (WebhookLog $record) => $this->resend($record))
                ->requiresConfirmation(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (WebhookLog $record) => route('webhooks.logs.show', [$this->webhook, $record]);
    }

    public function resend(WebhookLog $webhookLog)
    {
        $this->resendWebhookAction()->execute($webhookLog);
        $this->resetPage();
    }

    protected function resendWebhookAction(): ResendWebhookCallAction
    {
        /** @var ResendWebhookCallAction $action */
        $action = Mailcoach::getSharedActionClass('resend_webhook', ResendWebhookCallAction::class);

        return $action;
    }
}
