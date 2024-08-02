<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Postmark\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Postmark;
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
            $validApiKey = (new Postmark($this->apiKey))->hasValidServerToken();
        } catch (Exception) {
            notify('Something went wrong communicating with Postmark.', 'error');

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', 'This is not a valid Server API token.');

            return;
        }

        notify('The Server API token is correct.');

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
        return view('mailcoach::app.configuration.mailers.wizards.postmark.authentication');
    }
}
