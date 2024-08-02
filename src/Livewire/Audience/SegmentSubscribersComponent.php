<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Filament\Tables\Actions\Action;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Jobs\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class SegmentSubscribersComponent extends SubscribersComponent
{
    public EmailList $emailList;

    public TagSegment $segment;

    public function mount(EmailList $emailList, ?TagSegment $segment = null)
    {
        if (! $segment) {
            abort(404);
        }

        $this->emailList = $emailList;
        $this->segment = $segment;
    }

    public function getTitle(): string
    {
        return $this->segment->name;
    }

    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return [5];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-s-users';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __mc('No subscribers found');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Your segment might be a bit too exclusive.');
    }

    public function getTableQuery(): Builder
    {
        return $this->segment->getSubscribersQuery();
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'selectedSubscribersCount' => $this->segment->getSubscribersCount(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('export_subscribers')
                ->label(function () {
                    return __mc('Export :count subscribers', ['count' => Str::shortNumber($this->getAllTableRecordsCount())]);
                })
                ->requiresConfirmation()
                ->color('gray')
                ->icon('heroicon-s-cloud-arrow-down')
                ->action(function () {
                    $export = self::getSubscriberExportClass()::create([
                        'email_list_id' => $this->emailList->id,
                        'filters' => array_merge($this->tableFilters ?? [], [
                            'segment_id' => $this->segment->id,
                        ]),
                    ]);

                    dispatch(new ExportSubscribersJob(
                        subscriberExport: $export,
                        user: Auth::guard(config('mailcoach.guard'))->user(),
                    ));

                    notify(__mc('Subscriber export successfully queued.'));

                    return redirect()->route('mailcoach.emailLists.subscriber-exports', [$this->emailList]);
                }),
        ];
    }
}
