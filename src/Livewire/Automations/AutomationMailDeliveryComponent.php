<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailDeliveryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('view', $this->mail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations'));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.delivery')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Delivery'),
                'mail' => $this->mail,
            ]);
    }
}
