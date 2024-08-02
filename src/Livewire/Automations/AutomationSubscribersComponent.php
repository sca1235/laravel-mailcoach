<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class AutomationSubscribersComponent extends TableComponent
{
    public Automation $automation;

    public function mount(Automation $automation)
    {
        app(MainNavigation::class)->activeSection()?->add($automation->name, route('mailcoach.automations'));
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-user-group';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Where is everyone? No subscribers found');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('email')
                ->url(fn ($record) => route('mailcoach.emailLists.subscriber.details', [$record->emailList, $record]))
                ->extraAttributes(['class' => 'link'])
                ->searchable(),
            TextColumn::make('current_step')
                ->html()
                ->getStateUsing(function (Subscriber $record) {
                    $latestAction = $record->actions->where('automation_id', $this->automation->id)->sortByDesc('id')->first();

                    if (! $latestAction) {
                        return '';
                    }

                    $index = $this->automation->allActions->search(fn ($action) => $action->uuid === $latestAction->uuid) + 1;

                    $status = match (true) {
                        ! is_null($latestAction->pivot->halted_at) => __mc('halted'),
                        ! is_null($latestAction->pivot->completed_at) => __mc('completed'),
                        default => __mc('active'),
                    };

                    return <<<"html"
                        <span>{$index} - {$latestAction->action::getName()} <span class="ml-1 bg-sky-extra-light py-1 px-2 text-xs rounded-full text-navy">{$status}</span></span>
                    html;
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('action')
                ->label(__mc('Action'))
                ->options(fn () => $this->automation->actions->mapWithKeys(fn (Action $action, int $index) => [$action->id => ($index + 1).' - '.$action->action::getName()]))
                ->query(function (Builder $query, array $data) {
                    if (! $data['value']) {
                        return $query;
                    }

                    return $query->whereHas('actions', function (Builder $query) use ($data) {
                        $query->where('action_id', $data['value']);
                    });
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSubscriberClass()::query()
            ->with('emailList', 'actions')
            ->whereHas('actions', function (Builder $query) {
                $query->whereIn('action_id', $this->automation->allActions()->pluck('id'));
            });
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.automations.layouts.automation';
    }

    public function getLayoutData(): array
    {
        return [
            'automation' => $this->automation,
            'title' => $this->automation->name,
            'originTitle' => __mc('Automations'),
            'originHref' => route('mailcoach.automations'),
        ];
    }
}
