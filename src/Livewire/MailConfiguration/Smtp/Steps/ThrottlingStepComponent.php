<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Smtp\Steps;

use Spatie\Mailcoach\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 1;

    public int $mailsPerTimeSpan = 10;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.smtp.throttling');
    }
}
