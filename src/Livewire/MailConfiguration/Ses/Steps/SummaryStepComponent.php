<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Ses\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSes;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSesConfig;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class SummaryStepComponent extends StepComponent
{
    use UsesMailer;

    public int $mailerId;

    public function render()
    {
        $mailer = $this->mailer();

        $config = new MailcoachSesConfig(
            $mailer->get('ses_key'),
            $mailer->get('ses_secret'),
            $mailer->get('ses_region'),
        );

        $mailcoachSes = (new MailcoachSes($config));

        $isInSandboxMode = $mailcoachSes->isInSandboxMode();

        return view('mailcoach::app.configuration.mailers.wizards.ses.summary', [
            'isInSandboxMode' => $isInSandboxMode,
            'mailer' => $mailer,
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
