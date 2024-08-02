<?php

namespace Spatie\Mailcoach\Domain\Settings\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Settings\Mail\WebhookDisabledMail;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Mailcoach;

class SendWebhookDisabledMailAction
{
    public function execute(WebhookConfiguration $webhookConfiguration): void
    {
        if (! config('mailcoach.webhooks.notified_emails')) {
            return;
        }

        Mail::mailer(Mailcoach::defaultSystemMailer())->send(new WebhookDisabledMail(
            recipients: config('mailcoach.webhooks.notified_emails'),
            webhookConfiguration: $webhookConfiguration,
        ));
    }
}
