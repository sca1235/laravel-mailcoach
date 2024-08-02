<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Smtp\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class SmtpSettingsStepComponent extends StepComponent
{
    use UsesMailer;

    public string $host = '';

    public ?int $port;

    public ?string $username = '';

    public ?string $password = '';

    public string $encryption = '';

    public $rules = [
        'host' => 'required',
        'port' => 'required',
        'username' => ['nullable', 'string'],
        'password' => ['nullable', 'string'],
        'encryption' => '',
    ];

    public function mount()
    {
        $this->host = $this->mailer()->get('host', '');
        $this->port = $this->mailer()->get('port', 25);
        $this->username = $this->mailer()->get('username', '');
        $this->password = $this->mailer()->get('password', '');
        $this->encryption = $this->mailer()->get('encryption', '');
    }

    public function submit()
    {
        $this->validate();

        notify('Your credentials were correct.');

        $this->mailer()->merge([
            'driver' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
        ]);

        notify('Settings saved');

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'SMTP settings',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.smtp.settings', [
            'encryptionOptions' => $this->encryptionOptions(),
        ]);
    }

    public function encryptionOptions()
    {
        return [
            '' => 'None',
            'tls' => 'TLS',
        ];
    }
}
