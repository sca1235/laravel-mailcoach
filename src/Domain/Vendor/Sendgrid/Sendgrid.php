<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Enums\EventType;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Exceptions\CouldNotAccessAccountSetting;

class Sendgrid
{
    public function __construct(protected string $apiKey)
    {
    }

    public function isValidApiKey(): bool
    {
        return $this->callSendGrid('v3/user/webhooks/event/settings')->successful();
    }

    public function setupWebhook(string $url, array $events): array
    {
        $eventValues = collect(EventType::cases())
            ->mapWithKeys(function (EventType $eventType) use ($events) {
                return [$eventType->value => in_array($eventType, $events)];
            })
            ->toArray();

        $payload = array_merge([
            'enabled' => true,
            'url' => $url,
        ], $eventValues);

        $response = $this->callSendGrid(
            'v3/user/webhooks/event/settings',
            'patch',
            $payload
        );

        $enableOpenTracking = in_array(EventType::Open, $events);
        $this->enableOpenTracking(enabled: $enableOpenTracking);

        $enableClickTracking = in_array(EventType::Click, $events);
        $this->enableClickTracking($enableClickTracking);

        return $response->json();
    }

    public function enableOpenTracking(bool $enabled = true): array
    {
        $response = $this->callSendGrid('v3/tracking_settings/open', 'patch', [
            'enabled' => $enabled,
        ]);

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: open');
        }

        return $response->json();
    }

    public function openTrackingEnabled(): bool
    {
        $response = $this->callSendGrid('v3/tracking_settings/open');

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: open');
        }

        return $response->json('enabled');
    }

    public function enableClickTracking(bool $enabled = true): array
    {
        $response = $this->callSendGrid('v3/tracking_settings/click', 'patch', [
            'enabled' => $enabled,
        ]);

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: click');
        }

        return $response->json();
    }

    public function clickTrackingEnabled(): bool
    {
        $response = $this->callSendGrid('v3/tracking_settings/click');

        if (! $response->successful()) {
            throw CouldNotAccessAccountSetting::make('tracking settings: click');
        }

        return $response->json('enabled');
    }

    public function getWebhook(): array
    {
        return $this->callSendGrid('v3/user/webhooks/event/settings')->json();
    }

    protected function callSendGrid(string $endpoint, string $httpVerb = 'get', array $payload = []): Response
    {
        return Http::withToken($this->apiKey)->$httpVerb("https://api.sendgrid.com/$endpoint", $payload);
    }
}
