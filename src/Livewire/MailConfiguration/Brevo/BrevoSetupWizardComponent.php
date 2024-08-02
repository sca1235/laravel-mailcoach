<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Brevo;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps\ThrottlingStepComponent;

class BrevoSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::brevo-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::brevo-summary-step' => [
                'mailerId' => $this->mailer->id,
            ],
        ];
    }

    public function steps(): array
    {
        return [
            AuthenticationStepComponent::class,
            ThrottlingStepComponent::class,
            FeedbackStepComponent::class,
            SummaryStepComponent::class,
        ];
    }

    public static function registerLivewireComponents(): void
    {
        Livewire::component('mailcoach::brevo-configuration', BrevoSetupWizardComponent::class);

        Livewire::component('mailcoach::brevo-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::brevo-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::brevo-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::brevo-summary-step', SummaryStepComponent::class);
    }
}
