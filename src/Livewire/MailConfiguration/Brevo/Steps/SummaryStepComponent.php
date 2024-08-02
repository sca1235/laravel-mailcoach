<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class SummaryStepComponent extends StepComponent
{
    use UsesMailer;

    public int $mailerId;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.brevo.summary', [
            'mailer' => $this->mailer(),
        ]);
    }

    public function sendTestEmail()
    {
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Summary',
        ];
    }
}
