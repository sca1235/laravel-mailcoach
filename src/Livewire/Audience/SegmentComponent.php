<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Rules\StoredConditionRule;
use Spatie\Mailcoach\MainNavigation;

class SegmentComponent extends Component
{
    use AuthorizesRequests;

    public string $tab = 'details';

    public EmailList $emailList;

    public TagSegment $segment;

    public array $storedConditions;

    public string $name;

    protected $listeners = [
        'storedConditionsUpdated' => 'updateStoredConditions',
    ];

    protected array $queryString = [
        'tab' => ['except' => 'details'],
    ];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'storedConditions' => ['array'],
            //'storedConditions.*' => [new StoredConditionRule()], // @todo get this working
        ];
    }

    public function mount(EmailList $emailList, TagSegment $segment, MainNavigation $mainNavigation): void
    {
        $this->authorize('update', $emailList);
        $this->authorize('update', $segment);

        $this->emailList = $emailList;
        $this->segment = $segment;

        $this->name = $this->segment->name;
        $this->storedConditions = $this->segment->stored_conditions->castToArray();

        $mainNavigation->activeSection()
            ?->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Segments'), route('mailcoach.emailLists.segments', $this->emailList));
            });
    }

    public function save(): void
    {
        $this->validate();

        $this->segment->update([
            'name' => $this->name,
            'stored_conditions' => StoredConditionCollection::fromRequest($this->storedConditions),
        ]);

        notify(__mc('The segment has been updated.'));
        $this->dispatch('segmentUpdated');
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.segments.show')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => $this->segment->name,
                'originTitle' => $this->emailList->name,
                'originHref' => route('mailcoach.emailLists.segments', $this->emailList),
                'emailList' => $this->emailList,
            ]);
    }

    public function updateStoredConditions(array $storedConditions): void
    {
        $this->storedConditions = $storedConditions;
    }
}
