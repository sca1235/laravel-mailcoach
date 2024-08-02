<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag as TagModel;
use Spatie\Mailcoach\MainNavigation;

class TagComponent extends Component
{
    use AuthorizesRequests;

    public EmailList $emailList;

    public TagModel $tag;

    public ?string $name;

    public ?string $description = null;

    public bool $visible_in_preferences = false;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('mailcoach_tags', 'name')
                    ->where('email_list_id', $this->emailList->id)
                    ->ignore($this->tag->id),
            ],
            'description' => ['nullable', 'string'],
            'visible_in_preferences' => ['required', 'bool'],
        ];
    }

    public function mount(EmailList $emailList, TagModel $tag)
    {
        $this->authorize('update', $emailList);
        $this->authorize('update', $tag);

        $this->emailList = $emailList;
        $this->tag = $tag;
        $this->fill($this->tag->toArray());

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Tags'), route('mailcoach.emailLists.tags', $this->emailList));
            });
    }

    public function save()
    {
        $this->validate();

        $this->tag->fill($this->only(['name', 'description', 'visible_in_preferences']));
        $this->tag->save();

        notify(__mc('Tag :tag was updated', ['tag' => $this->tag->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.tags.show')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'emailList' => $this->emailList,
                'title' => $this->tag->name,
                'originTitle' => $this->emailList->name,
                'originHref' => route('mailcoach.emailLists.tags', $this->emailList),
            ]);
    }
}
