<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Settings\Actions\GetWebhookConfigurationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookFailedAttemptsSubscriber
{
    use UsesMailcoachModels;

    public function __construct(
        private GetWebhookConfigurationAction $getWebhookConfigurationAction
    ) {
    }

    public function subscribe(): array
    {
        if (! is_int(config('mailcoach.webhooks.maximum_attempts')) ||
            config('mailcoach.webhooks.maximum_attempts') === 0) {
            return [];
        }

        if (! config('mailcoach.opt_in_features.disable_failed_webhooks', false)) {
            return [];
        }

        return [
            WebhookCallSucceededEvent::class => 'handleWebhookEvent',
            FinalWebhookCallFailedEvent::class => 'handleWebhookEvent',
        ];
    }

    public function handleWebhookEvent(WebhookCallEvent $event)
    {
        if (! config('mailcoach.opt_in_features.disable_failed_webhooks', false)) {
            return [];
        }

        $webhookConfiguration = $this->getWebhookConfigurationAction->execute($event);

        if (! $webhookConfiguration) {
            return;
        }

        if ($event instanceof WebhookCallSucceededEvent) {
            $webhookConfiguration->resetFailedAttempts();
        }

        if ($event instanceof FinalWebhookCallFailedEvent) {
            $webhookConfiguration->incrementFailedAttempts();
        }
    }
}
