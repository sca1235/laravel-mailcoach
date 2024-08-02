<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class FeedbackStepComponent extends StepComponent
{
    use UsesMailer;

    public string $signingSecret = '';

    public array $rules = [
        'signingSecret' => ['required'],
    ];

    public function mount(): void
    {
        $this->signingSecret = $this->mailer()->get('signing_secret', '');
    }

    public function configureResend(): void
    {
        $this->validate();

        $this->mailer()->merge([
            'signing_secret' => $this->signingSecret,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.resend.feedback');
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
