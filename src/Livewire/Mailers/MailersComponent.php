<?php

namespace Spatie\Mailcoach\Livewire\Mailers;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\TableComponent;

class MailersComponent extends TableComponent
{
    use UsesMailcoachModels;

    public function getTitle(): string
    {
        return __mc('Mailers');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-envelope-open';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('You have not added any mailer yet.');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('mailer-docs')
                ->url('https://mailcoach.app/resources/learn-mailcoach/getting-started/using-an-external-mailer')
                ->openUrlInNewTab()
                ->label(__mc('Learn more about mailers'))
                ->link(),
            Action::make('configuration-docs')
                ->url('https://mailcoach.app/resources/learn-mailcoach/getting-started/configuring-mail-providers')
                ->openUrlInNewTab()
                ->label(__mc('Configuring providers'))
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
            'title' => __mc('Mailers'),
            'create' => 'mailer',
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->extraAttributes(['class' => 'link'])
                ->sortable(),
            TextColumn::make('transport')
                ->label(__mc('Transport'))
                ->sortable(),
            IconColumn::make('ready_for_use')->boolean(),
            IconColumn::make('default')->boolean(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Mailer $record) => route('mailers.edit', $record);
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('make-default')
                    ->label(__mc('Make default'))
                    ->icon('heroicon-s-check-circle')
                    ->requiresConfirmation()
                    ->hidden(fn (Mailer $record) => $record->default)
                    ->action(fn (Mailer $record) => $this->markMailerDefault($record)),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Mailer $record) => $this->deleteMailer($record)),
            ]),
        ];
    }

    public function markMailerDefault(Mailer $mailer)
    {
        if (! $mailer->ready_for_use) {
            notifyError(__mc('Mailer :mailer is not ready for use', ['mailer' => $mailer->name]));

            return;
        }

        self::getMailerClass()::query()->update(['default' => false]);

        $mailer->update(['default' => true]);

        notify(__mc('Mailer :mailer marked as default', ['mailer' => $mailer->name]));
    }

    public function deleteMailer(Mailer $mailer): void
    {
        $configName = $mailer->configName();

        $mailer->delete();

        self::getEmailListClass()::each(function (EmailList $emailList) use ($configName) {
            if ($emailList->campaign_mailer === $configName) {
                $emailList->campaign_mailer = null;
            }

            if ($emailList->automation_mailer === $configName) {
                $emailList->automation_mailer = null;
            }

            if ($emailList->transactional_mailer === $configName) {
                $emailList->transactional_mailer = null;
            }

            $emailList->save();
        });

        notify(__mc('Mailer :mailer successfully deleted', ['mailer' => $mailer->name]));
    }

    protected function getTableQuery(): Builder
    {
        return self::getMailerClass()::query();
    }
}
