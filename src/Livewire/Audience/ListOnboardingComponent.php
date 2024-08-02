<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Audience\Forms\ListOnboardingForm;
use Spatie\Mailcoach\MainNavigation;

class ListOnboardingComponent extends Component
{
    use UsesMailcoachModels;

    public EmailList $emailList;

    public ListOnboardingForm $form;

    public array $transactionalMailTemplates = [];

    protected $listeners = [
        'tags-updated-allowed_form_subscription_tags' => 'updateAllowedFormSubscriptionTags',
    ];

    public function updateAllowedFormSubscriptionTags(string ...$tags): void
    {
        $this->form->allowed_form_subscription_tags = $tags;
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList->load(['tags', 'allowedFormSubscriptionTags', 'confirmationMail']);
        $this->form->setEmailList($this->emailList);

        $this->transactionalMailTemplates = self::getTransactionalMailClass()::pluck('name', 'id')->toArray();

        app(MainNavigation::class)->activeSection()->add($this->emailList->name, route('mailcoach.emailLists.onboarding', $this->emailList));
    }

    public function messages(): array
    {
        $customMailRequiredValidationMessage = __mc('This field is required when using a custom mail');

        return [
            'form.confirmation_mail_id.required_if' => $customMailRequiredValidationMessage,
        ];
    }

    public function updated()
    {
        $this->form->dirty = true;
    }

    public function save()
    {
        $this->form->validate();
        $this->form->save();

        notify(__mc('List :emailList was updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.settings.onboarding')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __mc('Onboarding'),
                'emailList' => $this->emailList,
            ]);
    }
}
