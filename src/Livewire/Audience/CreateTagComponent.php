<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTagComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public ?string $name = null;

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    protected function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function saveTag()
    {
        $tagClass = self::getTagClass();

        $this->authorize('create', $tagClass);

        $tag = $this->emailList->tags()->create([
            'name' => $this->validate()['name'],
            'type' => TagType::Default,
        ]);

        notify(__mc('Tag :tag was created', ['tag' => $tag->name]));

        return redirect()->route('mailcoach.emailLists.tags.edit', [$this->emailList, $tag]);
    }

    public function render()
    {
        return view('mailcoach::app.emailLists.tags.partials.create');
    }
}
