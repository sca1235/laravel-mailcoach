<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo;

use Spatie\Mailcoach\Domain\Vendor\Brevo\Jobs\ProcessBrevoWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class BrevoWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.brevo_feedback');

        return new WebhookConfig([
            'name' => 'brevo-feedback',
            'signing_secret' => $config['signing_secret'] ?? '',
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? BrevoSignatureValidator::class,
            'webhook_profile' => $config['webhook_profile'] ?? ProcessEverythingWebhookProfile::class,
            'webhook_model' => $config['webhook_model'] ?? WebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessBrevoWebhookJob::class,
        ]);
    }
}
