<?php

namespace Spatie\Mailcoach\Domain\Settings\Actions;

use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\WebhookServer\WebhookCall;

class ResendWebhookCallAction
{
    public function execute(WebhookLog $webhookLog): void
    {
        WebhookCall::create()
            ->timeoutInSeconds(10)
            ->maximumTries(0)
            ->url($webhookLog->webhookConfiguration->url)
            ->payload($webhookLog->payload)
            ->useSecret($webhookLog->webhookConfiguration->secret)
            ->throwExceptionOnFailure()
            ->meta([
                'webhook_configuration_uuid' => $webhookLog->webhookConfiguration->uuid,
                'webhook_call_uuid' => $webhookLog->webhook_call_uuid,
                'manual' => 'true',
            ])
            ->dispatchSync();
    }
}
