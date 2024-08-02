<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Resend;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class SummaryStepComponent extends StepComponent
{
    use UsesMailer;

    public int $mailerId;

    public function render()
    {
        $domains = (new Resend($this->mailer()->get('apiKey')))->getDomains();

        return view('mailcoach::app.configuration.mailers.wizards.resend.summary', [
            'mailer' => $this->mailer(),
            'domains' => $domains,
        ]);
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Summary',
        ];
    }
}
