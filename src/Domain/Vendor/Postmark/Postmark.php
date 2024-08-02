<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Data\MessageStream;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Data\Webhook;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Enums\PostMarkTrigger;

class Postmark
{
    public function __construct(protected string $serverToken)
    {
    }

    public function hasValidServerToken(): bool
    {
        return $this->callPostmark('/server')->successful();
    }

    /** @return Collection<MessageStream> */
    public function getStreams(): Collection
    {
        return collect($this->callPostmark('/message-streams')->json('MessageStreams'))
            ->map(fn (array $stream) => new MessageStream(
                $stream['ID'],
                $stream['ServerID'],
                $stream['Name'],
            ));
    }

    public function getWebhook(string $url, string $streamId): ?Webhook
    {
        $existingWebhook = collect($this->callPostmark("webhooks?MessageStream={$streamId}")->json('Webhooks'))
            ->where('Url', $url)
            ->where('MessageStream', $streamId)
            ->first();

        if ($existingWebhook) {
            return Webhook::fromPayload($existingWebhook);
        }

        return null;
    }

    /**
     * @param  array<int, \Spatie\Mailcoach\Domain\Vendor\Postmark\Enums\PostMarkTrigger>  $triggers
     */
    public function configureWebhook(string $url, string $streamId, array $triggers = [], string $secret = ''): Response
    {
        $existingWebhook = $this->getWebhook($url, $streamId);

        $mappedTriggers = collect(PostMarkTrigger::cases())
            ->mapWithKeys(function (PostMarkTrigger $trigger) use ($triggers) {
                $triggerProperties = [
                    'enabled' => in_array($trigger, $triggers),
                ];

                if ($trigger === PostMarkTrigger::Open) {
                    $triggerProperties['PostFirstOpenOnly'] = false;
                }

                if ($trigger === PostMarkTrigger::Bounce) {
                    $triggerProperties['IncludeContent'] = false;
                }

                if ($trigger === PostMarkTrigger::SpamComplaint) {
                    $triggerProperties['IncludeContent'] = false;
                }

                return [$trigger->value => $triggerProperties];
            })
            ->toArray();

        $this->enableOpenTracking(in_array(PostMarkTrigger::Open, $triggers));
        $this->enableClickTracking(in_array(PostMarkTrigger::Click, $triggers));

        if ($existingWebhook) {
            return $this->callPostmark("/webhooks/{$existingWebhook->id}", 'put', [
                'Url' => $url,
                'Triggers' => $mappedTriggers,
                'HttpHeaders' => [
                    [
                        'Name' => 'mailcoach-signature',
                        'Value' => $secret,
                    ],
                ],
            ]);
        }

        return $this->callPostmark('/webhooks', 'post', [
            'Url' => $url,
            'Triggers' => $mappedTriggers,
            'MessageStream' => $streamId,
            'HttpHeaders' => [
                [
                    'Name' => 'mailcoach-signature',
                    'Value' => $secret,
                ],
            ],
        ]);
    }

    public function deleteWebhook(string $url, string $streamId): void
    {
        $webhook = $this->getWebhook($url, $streamId);

        if (is_null($webhook)) {
            return;
        }

        $this->callPostmark("/webhooks/{$webhook->id}", 'delete');
    }

    public function enableOpenTracking(bool $enabled = true): void
    {
        $this->callPostmark('/server', 'put', [
            'TrackOpens' => $enabled,
        ]);
    }

    public function openTrackingEnabled(): bool
    {
        return $this->callPostmark('/server')->json('TrackOpens');
    }

    public function enableClickTracking(bool $enabled = true): void
    {
        $this->callPostmark('/server', 'put', [
            'TrackLinks' => $enabled ? 'HtmlAndText' : 'None',
        ]);
    }

    public function clickTrackingEnabled(): bool
    {
        return $this->callPostmark('/server')->json('TrackLinks') === 'HtmlAndText';
    }

    protected function callPostmark(
        string $endpoint,
        string $httpVerb = 'get',
        array $payload = []
    ): Response {
        return Http::withHeaders([
            'X-Postmark-Server-Token' => $this->serverToken,
            'Content-Type' => 'application/json',
        ])->$httpVerb("https://api.postmarkapp.com/$endpoint", $payload);
    }
}
