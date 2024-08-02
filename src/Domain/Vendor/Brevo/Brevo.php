<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Spatie\Mailcoach\Domain\Vendor\Brevo\Enums\EventType;

class Brevo
{
    public function __construct(protected string $apiKey)
    {
    }

    public function isValidApiKey(): bool
    {
        return $this->callBrevo('account')->successful();
    }

    public function setupWebhook(string $url): void
    {
        $existingWebhook = $this->getWebhook($url);

        $payloadEvents = [
            EventType::Spam->value,
            EventType::Bounce->value,
            EventType::Open->value,
            EventType::Click->value,
        ];

        if ($existingWebhook) {
            $response = $this->callBrevo("webhooks/{$existingWebhook['id']}", 'put', [
                'url' => $url,
                'events' => $payloadEvents,
            ]);

            if (! $response->successful()) {
                throw new \Exception('Could not update webhook: '.$response->json('message'));
            }
        } else {
            $response = $this->callBrevo('webhooks', 'post', [
                'type' => 'transactional',
                'url' => $url,
                'events' => $payloadEvents,
            ]);

            if (! $response->successful()) {
                throw new \Exception('Could not create webhook: '.$response->json('message'));
            }
        }
    }

    public function getWebhook(string $url): ?array
    {
        $webhooks = $this->callBrevo('webhooks')->json('webhooks');

        return collect($webhooks)->where('url', $url)->first();
    }

    public function deleteWebhook(string $url): void
    {
        $webhook = $this->getWebhook($url);

        if (! $webhook) {
            return;
        }

        $response = $this->callBrevo("webhooks/{$webhook['id']}", 'delete');

        if (! $response->successful()) {
            throw new \Exception("Could not delete webhook: {$response->json('message')}");
        }
    }

    protected function callBrevo(string $endpoint, string $httpVerb = 'get', array $payload = []): Response
    {
        return Http::withHeaders(['api-key' => $this->apiKey])
            ->$httpVerb("https://api.brevo.com/v3/$endpoint", $payload);
    }
}
