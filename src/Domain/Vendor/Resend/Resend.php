<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Resend
{
    public function __construct(protected string $apiKey)
    {
    }

    public function isValidApiKey(): bool
    {
        return $this->callResend('domains')->successful();
    }

    public function getDomains(): array
    {
        return $this->callResend('domains')->json('data') ?? [];
    }

    protected function callResend(string $endpoint, string $httpVerb = 'get', array $payload = []): Response
    {
        return Http::withHeader('Authorization', $this->apiKey)
            ->asJson()
            ->$httpVerb("https://api.resend.com/{$endpoint}", $payload);
    }
}
