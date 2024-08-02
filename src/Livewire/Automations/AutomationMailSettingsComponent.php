<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;
use Spatie\ValidationRules\Rules\Delimited;

class AutomationMailSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    #[Validate('required')]
    public string $name;

    #[Validate(['nullable', 'email:strict'])]
    public ?string $from_email;

    #[Validate(['nullable'])]
    public ?string $from_name;

    #[Validate(['nullable', new Delimited('email:strict')])]
    public ?string $reply_to_email;

    #[Validate(['nullable', new Delimited('string')])]
    public ?string $reply_to_name;

    #[Validate('bool')]
    public bool $utm_tags;

    #[Validate('bool')]
    public bool $add_subscriber_tags;

    #[Validate('bool')]
    public bool $add_subscriber_link_tags;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('update', $this->mail);

        $this->fill($this->mail->toArray());
        $this->fill($this->mail->contentItem->toArray());

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations'));
    }

    public function save()
    {
        $this->validate();

        $this->mail->update([
            'name' => $this->name,
        ]);
        $this->mail->contentItem->update(Arr::except($this->all(), ['name', 'mail']));

        notify(__mc('Email :name was updated.', ['name' => $this->mail->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.settings')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Settings'),
                'mail' => $this->mail,
            ]);
    }
}
