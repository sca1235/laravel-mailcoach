<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Resend;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps\ThrottlingStepComponent;

class ResendSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::resend-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::resend-summary-step' => [
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
        Livewire::component('mailcoach::resend-configuration', ResendSetupWizardComponent::class);

        Livewire::component('mailcoach::resend-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::resend-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::resend-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::resend-summary-step', SummaryStepComponent::class);
    }
}
