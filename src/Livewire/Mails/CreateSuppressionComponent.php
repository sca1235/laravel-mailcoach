<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateSuppressionComponent extends Component
{
    use UsesMailcoachModels;

    public string $email = '';

    public function save()
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        self::getSuppressionClass()::create([
            'email' => $this->email,
            'reason' => SuppressionReason::manual,
        ]);

        notify(__mc('The suppression has been created.'));

        return redirect()->route('suppressions');
    }

    public function render()
    {
        return view('mailcoach::app.configuration.suppressions.create');
    }
}
