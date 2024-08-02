<?php

namespace Spatie\Mailcoach\Domain\Settings\Actions;

use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookServer\Events\WebhookCallEvent;

class GetWebhookConfigurationAction
{
    use UsesMailcoachModels;

    public function execute(WebhookCallEvent $event): ?WebhookConfiguration
    {
        if (! isset($event->meta['webhook_configuration_uuid'])) {
            return null;
        }

        $webhookConfiguration = self::getWebhookConfigurationClass()::findByUuid($event->meta['webhook_configuration_uuid']);

        if (! $webhookConfiguration) {
            return null;
        }

        return $webhookConfiguration;
    }
}
