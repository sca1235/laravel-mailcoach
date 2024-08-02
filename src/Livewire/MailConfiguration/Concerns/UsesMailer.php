<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Concerns;

use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

trait UsesMailer
{
    use UsesMailcoachModels;

    private ?Mailer $mailer = null;

    public function mailer(): Mailer
    {
        if ($this->mailer) {
            return $this->mailer;
        }

        $mailerId = collect($this->state()->all())->firstWhere('mailerId')['mailerId'];

        $this->mailer = self::getMailerClass()::find($mailerId);

        return $this->mailer;
    }
}
