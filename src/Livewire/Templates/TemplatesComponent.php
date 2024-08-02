<?php

namespace Spatie\Mailcoach\Livewire\Templates;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Livewire\TableComponent;

class TemplatesComponent extends TableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getTemplateClass()::query();
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
                ->searchable(self::getTemplateClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0])
                ->size('base')
                ->extraAttributes(['class' => 'link']),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Template $record) {
            return route('mailcoach.templates.edit', $record);
        };
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (Template $record) => $this->duplicateTemplate($record))
                    ->icon('heroicon-s-document-duplicate')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(function (Template $record) {
                        $record->delete();
                        notify(__mc('Template :template was deleted.', ['template' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-s-trash')
                    ->color('danger'),
            ]),
        ];
    }

    public function duplicateTemplate(Template $template)
    {
        $this->authorize('create', self::getTemplateClass());

        $duplicateTemplate = self::getTemplateClass()::create([
            'name' => $template->name.' - '.__mc('copy'),
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        notify(__mc('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }

    public function getTitle(): string
    {
        return __mc('Templates');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-document-text';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('You have not created any templates yet.');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('learn')
                ->url('https://mailcoach.app/resources/learn-mailcoach/features/templates')
                ->label(__mc('Learn more about templates'))
                ->openUrlInNewTab()
                ->link(),
        ];
    }

    public function getLayoutData(): array
    {
        return [
            'hideBreadcrumbs' => true,
            'create' => 'template',
        ];
    }
}
