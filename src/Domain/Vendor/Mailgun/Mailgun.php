<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Enums\EventType;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Exceptions\CouldNotAccessAccountSetting;

class Mailgun
{
    public function __construct(protected string $apiKey, protected string $domain, protected string $baseUrl = 'api.mailgun.net')
    {
    }

    public function isValidApiKey(): bool
    {
        return $this->callMailgun("v3/domains/{$this->domain}/webhooks")->successful();
    }

    public function setupWebhook(string $url, array $events): void
    {
        /** @var EventType $event */
        foreach ($events as $event) {
            if ($this->hasWebhook($event)) {
                $this->callMailgun("v3/domains/{$this->domain}/webhooks/{$event->value}", 'put', [
                    'url' => $url,
                ]);

                continue;
            }

            $this->callMailgun("v3/domains/{$this->domain}/webhooks", 'post', [
                'id' => $event->value,
                'url' => $url,
            ]);
        }

        $enableOpenTracking = in_array(EventType::Opened, $events);
        $this->enableOpenTracking(enabled: $enableOpenTracking);

        $enableClickTracking = in_array(EventType::Clicked, $events);
        $this->enableClickTracking($enableClickTracking);
    }

    public function enableOpenTracking(bool $enabled = true): array
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/tracking/open", 'put', [
            'active' => $enabled ? 'yes' : 'no',
        ]);

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: open');
        }

        return $response->json();
    }

    public function openTrackingEnabled(): bool
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/tracking");

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: open');
        }

        return $response->json('tracking.open.active');
    }

    public function enableClickTracking(bool $enabled = true): array
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/tracking/click", 'put', [
            'active' => $enabled ? 'yes' : 'no',
        ]);

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: click');
        }

        return $response->json();
    }

    public function clickTrackingEnabled(): bool
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/tracking");

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: click');
        }

        return $response->json('tracking.click.active');
    }

    public function hasWebhook(EventType $type): bool
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/webhooks/{$type->value}");

        if (! $response->successful()) {
            return false;
        }

        return count($response->json('webhook.urls')) > 0;
    }

    public function deleteWebhook(EventType $type): void
    {
        $response = $this->callMailgun("v3/domains/{$this->domain}/webhooks/{$type->value}", 'delete');

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('delete webhook');
        }
    }

    protected function callMailgun(string $endpoint, string $httpVerb = 'get', array $payload = []): Response
    {
        return Http::withBasicAuth('api', $this->apiKey)->asForm()->$httpVerb("https://{$this->baseUrl}/{$endpoint}", $payload);
    }
}
