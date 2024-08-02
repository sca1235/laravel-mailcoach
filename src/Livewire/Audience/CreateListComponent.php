<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Actions\EmailLists\CreateEmailListAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateListComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public ?string $name = null;

    public ?string $default_from_email = null;

    public ?string $default_from_name = null;

    protected function rules()
    {
        return [
            'name' => 'required',
            'default_from_email' => 'required|email:strict',
            'default_from_name' => '',
        ];
    }

    public function mount()
    {
        $this->default_from_email = Auth::guard(config('mailcoach.guard'))->user()->email;
        $this->default_from_name = Auth::guard(config('mailcoach.guard'))->user()->name;
    }

    public function saveList()
    {
        $emailListClass = self::getEmailListClass();

        $this->authorize('create', $emailListClass);

        $emailList = resolve(CreateEmailListAction::class)->execute(
            new $emailListClass,
            $this->validate(),
        );

        notify(__mc('List :emailList was created', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.general-settings', $emailList);
    }

    public function render()
    {
        return view('mailcoach::app.emailLists.partials.create');
    }
}
