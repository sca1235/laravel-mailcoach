<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Resend\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Resend\Resend;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class AuthenticationStepComponent extends StepComponent
{
    use UsesMailer;

    public string $apiKey = '';

    public $rules = [
        'apiKey' => ['required'],
    ];

    public function mount()
    {
        $this->apiKey = $this->mailer()->get('apiKey', '');
    }

    public function submit()
    {
        $this->validate();

        try {
            $validApiKey = (new Resend($this->apiKey))->isValidApiKey();
        } catch (Exception) {
            notify('Something went wrong communicating with Resend.', 'error');

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', __mc('These credentials are not valid.'));

            return;
        }

        notify('The credentials are correct.');

        $this->mailer()->merge([
            'apiKey' => $this->apiKey,
        ]);

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Authenticate',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.resend.authentication');
    }
}
