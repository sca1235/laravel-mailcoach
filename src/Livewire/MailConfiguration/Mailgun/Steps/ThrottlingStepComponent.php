<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Mailgun\Steps;

use Spatie\Mailcoach\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 1;

    public int $mailsPerTimeSpan = 50;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.mailgun.throttling');
    }
}
