<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public Automation $automation;

    public bool $dirty = false;

    public string $name;

    public ?int $email_list_id;

    public ?int $segment_id;

    public ?bool $repeat_enabled;

    public ?bool $repeat_only_after_halt;

    public string $segment;

    public Collection $emailLists;

    public Collection $segmentsData;

    protected function rules(): array
    {
        return [
            'name' => ['required'],
            'email_list_id' => [Rule::exists(self::getEmailListClass(), 'id')],
            'segment_id' => ['required_if:segment,segment'],
            'repeat_enabled' => ['nullable', 'boolean'],
            'repeat_only_after_halt' => ['nullable', 'boolean'],
            'segment' => [Rule::in(['entire_list', 'segment'])],
        ];
    }

    public function mount(Automation $automation)
    {
        $this->authorize('update', $automation);

        $this->automation = $automation;
        $this->fill($this->automation->only(
            'name',
            'email_list_id',
            'segment_id',
            'repeat_enabled',
            'repeat_only_after_halt',
        ));

        $this->emailLists = self::getEmailListClass()::all();

        $this->segmentsData = $this->emailLists->map(function (EmailList $emailList) {
            return [
                'id' => $emailList->id,
                'name' => $emailList->name,
                'segments' => $emailList->segments()->orderBy('name')->pluck('name', 'id')->toArray(),
                'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
            ];
        });

        $this->segment = $automation->notSegmenting() ? 'entire_list' : 'segment';

        app(MainNavigation::class)->activeSection()?->add($automation->name, route('mailcoach.automations'));
    }

    public function updated()
    {
        $this->dirty = true;
    }

    public function save(string $formData)
    {
        parse_str($formData, $data);

        $this->validate();

        $this->automation->fill([
            'name' => $this->name,
            'email_list_id' => $this->email_list_id,
            'repeat_enabled' => $this->repeat_enabled,
            'repeat_only_after_halt' => $this->repeat_only_after_halt,
        ]);

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($this->segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->automation->usingCustomSegment()) {
            $segmentClass = $this->automation->segment_class;
        }

        $this->automation->fill([
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $data['segment_id'],
        ]);

        $this->automation->save();

        $this->automation->update(['segment_description' => $this->automation->getSegment()->description()]);

        notify(__mc('Automation :automation was updated.', ['automation' => $this->automation->name]));

        $this->dirty = false;
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.settings')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => $this->automation->name,
                'originTitle' => __mc('Automations'),
                'originHref' => route('mailcoach.automations'),
            ]);
    }
}
