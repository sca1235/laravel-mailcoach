<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\SendGrid\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Sendgrid;
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
            $validApiKey = (new Sendgrid($this->apiKey))->isValidApiKey();
        } catch (Exception) {
            notify('Something went wrong communicating with SendGrid.', 'error');

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', 'This is not a valid API key.');

            return;
        }

        notify('The API key is correct.');

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
        return view('mailcoach::app.configuration.mailers.wizards.sendGrid.authentication');
    }
}
