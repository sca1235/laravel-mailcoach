<?php

namespace Spatie\Mailcoach\Domain\Settings\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\WebhookServer\WebhookCall;

class SendWebhookAction
{
    public function execute(EmailList $emailList, array $payload, object $event): void
    {
        $payload['event'] = class_basename($event);

        $emailList->webhookConfigurations()->each(
            fn (WebhookConfiguration $webhookConfiguration) => $this->sendWebhookIfNeeded(
                $webhookConfiguration,
                $payload,
            )
        );
    }

    protected function sendWebhookIfNeeded(WebhookConfiguration $webhookConfiguration, array $payload): void
    {
        if (! $webhookConfiguration->enabled) {
            return;
        }

        if (! $this->webhookEnabledForEvent($webhookConfiguration, $payload['event'])) {
            return;
        }

        if ($this->webhookExceededMaximumTries($webhookConfiguration)) {
            return;
        }

        $this->sendWebhook($webhookConfiguration, $payload);
    }

    protected function webhookExceededMaximumTries(WebhookConfiguration $webhookConfiguration): bool
    {
        if (! config('mailcoach.opt_in_features.disable_failed_webhooks', false)) {
            return false;
        }

        if ($webhookConfiguration->maximumAttempts() === null) {
            return false;
        }

        return $webhookConfiguration->failed_attempts >= $webhookConfiguration->maximumAttempts();
    }

    protected function webhookEnabledForEvent(WebhookConfiguration $webhookConfiguration, string $event): bool
    {
        if (! config('mailcoach.webhooks.selectable_event_types_enabled', true)) {
            return true;
        }

        if ($webhookConfiguration->events === null || count($webhookConfiguration->events) === 0) {
            return true;
        }

        if ($webhookConfiguration->events->contains($event)) {
            return true;
        }

        return false;
    }

    protected function sendWebhook(WebhookConfiguration $webhookConfiguration, array $payload): void
    {
        WebhookCall::create()
            ->onQueue(config('mailcoach.perform_on_queue.send_webhooks'))
            ->timeoutInSeconds(10)
            ->maximumTries(5)
            ->url($webhookConfiguration->url)
            ->payload($payload)
            ->useSecret($webhookConfiguration->secret)
            ->throwExceptionOnFailure()
            ->meta([
                'webhook_configuration_uuid' => $webhookConfiguration->uuid,
                'webhook_call_uuid' => Str::uuid(),
            ])
            ->dispatch();
    }
}
