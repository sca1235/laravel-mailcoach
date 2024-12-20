<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend;

use Spatie\Mailcoach\Domain\Vendor\Resend\Jobs\ProcessResendWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class ResendWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.resend_feedback');

        return new WebhookConfig([
            'name' => 'resend-feedback',
            'signing_secret' => $config['signing_secret'] ?? '',
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? ResendSignatureValidator::class,
            'webhook_profile' => $config['webhook_profile'] ?? ProcessEverythingWebhookProfile::class,
            'webhook_model' => $config['webhook_model'] ?? WebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessResendWebhookJob::class,
        ]);
    }
}
