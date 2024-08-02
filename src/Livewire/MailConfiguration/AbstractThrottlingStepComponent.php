<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

abstract class AbstractThrottlingStepComponent extends StepComponent
{
    use UsesMailer;

    public int $timespanInSeconds = 1;

    public int $mailsPerTimeSpan = 10;

    public array $rules = [
        'timespanInSeconds' => 'required|numeric|gte:1',
        'mailsPerTimeSpan' => 'required|numeric|gte:1',
    ];

    public function mount()
    {
        $this->timespanInSeconds = $this->mailer()->get('timespan_in_seconds', $this->timespanInSeconds);
        $this->mailsPerTimeSpan = $this->mailer()->get('mails_per_timespan', $this->mailsPerTimeSpan);
    }

    public function submit()
    {
        $this->validate();

        $this->mailer()->merge([
            'timespan_in_seconds' => $this->timespanInSeconds,
            'mails_per_timespan' => $this->mailsPerTimeSpan,
        ]);

        notify('The throttling settings were saved.');

        $this->nextStep();
    }

    abstract public function render();

    public function stepInfo(): array
    {
        return [
            'label' => 'Throttling',
        ];
    }
}
