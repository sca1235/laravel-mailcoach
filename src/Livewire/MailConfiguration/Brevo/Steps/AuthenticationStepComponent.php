<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Brevo;
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
            $validApiKey = (new Brevo($this->apiKey))->isValidApiKey();
        } catch (Exception) {
            notifyError(__mc('Something went wrong communicating with Brevo.'));

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', __mc('This is not a valid API key.'));

            return;
        }

        notify(__mc('The API key is correct.'));

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
        return view('mailcoach::app.configuration.mailers.wizards.brevo.authentication');
    }
}
