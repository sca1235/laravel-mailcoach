<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Audience\Forms\ListSettingsForm;
use Spatie\Mailcoach\MainNavigation;

class ListSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public EmailList $emailList;

    public ListSettingsForm $form;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
        $this->form->setEmailList($emailList);

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists.general-settings', $this->emailList));
    }

    public function updated()
    {
        $this->form->dirty = true;
    }

    public function save()
    {
        $this->form->update();

        notify(__mc('List :emailList was updated', ['emailList' => $this->emailList->name]));

        $this->form->dirty = false;
    }

    public function render(): View
    {
        $this->authorize('update', $this->emailList);

        return view('mailcoach::app.emailLists.settings.general')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __mc('General'),
                'emailList' => $this->emailList,
            ]);
    }
}
