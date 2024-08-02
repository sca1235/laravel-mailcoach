<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Support\LinkHasher;
use Spatie\Mailcoach\Mailcoach;

class ClicksComponent extends ContentItemTable
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'unique_click_count';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTitle(): string
    {
        return __mc('Clicks');
    }

    public function getTableRecordKey(Model $record): string
    {
        return 'uuid';
    }

    protected function getTableQuery(): Builder
    {
        return self::getLinkClass()::query()
            ->whereIn('content_item_id', $this->contentItems->pluck('id'))
            ->groupBy('url')
            ->select(
                Mailcoach::isPostgresqlDatabase() ? \DB::raw('string_agg(uuid::text, \',\') as uuid') : \DB::raw('group_concat(uuid) as uuid'),
                'url',
                \DB::raw('sum(unique_click_count) as unique_click_count'),
                \DB::raw('sum(click_count) as click_count')
            );
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No clicks');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-cursor-arrow-rays';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (method_exists($this->model, 'tracking')) {
            [$openTracking, $clickTracking] = $this->model->tracking();

            if ($openTracking || $clickTracking) {
                return __mc('No clicks yet. Stay tuned.');
            }
        }

        return __mc('No clicks tracked.');
    }

    protected function getTableColumns(): array
    {
        $searchable = $this->getTableQuery()->count() > $this->getTableRecordsPerPageSelectOptions()[0];

        return array_filter([
            TextColumn::make('url')
                ->label(__mc('Link'))
                ->sortable()
                ->extraAttributes(['class' => 'link'])
                ->searchable($searchable),
            $this->contentItems->filter->add_subscriber_link_tags->count()
                ? TextColumn::make('tag')
                    ->label(__mc('Tag'))
                    ->getStateUsing(fn (Link $record) => '<span class="tag-neutral">'.LinkHasher::hash($this->model, $record->url).'</span>')
                    ->html()
                : null,
            TextColumn::make('unique_click_count')
                ->label(__mc('Unique clicks'))
                ->sortable()
                ->alignRight()
                ->numeric(),
            TextColumn::make('click_count')
                ->label(__mc('Clicks'))
                ->sortable()
                ->alignRight()
                ->numeric(),
        ]);
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Link $record) {
            return match (true) {
                $this->model instanceof Campaign => route('mailcoach.campaigns.link-clicks', [$this->model, $record->uuid]),
                $this->model instanceof AutomationMail => route('mailcoach.automations.mails.link-clicks', [$this->model, $record->uuid]),
            };
        };
    }
}
