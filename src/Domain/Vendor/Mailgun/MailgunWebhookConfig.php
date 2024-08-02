<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun;

use Spatie\Mailcoach\Domain\Vendor\Mailgun\Jobs\ProcessMailgunWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class MailgunWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.mailgun_feedback');

        return new WebhookConfig([
            'name' => 'mailgun-feedback',
            'signing_secret' => $config['signing_secret'] ?? '',
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? MailgunSignatureValidator::class,
            'webhook_profile' => $config['webhook_profile'] ?? ProcessEverythingWebhookProfile::class,
            'webhook_model' => $config['webhook_model'] ?? WebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessMailgunWebhookJob::class,
        ]);
    }
}
