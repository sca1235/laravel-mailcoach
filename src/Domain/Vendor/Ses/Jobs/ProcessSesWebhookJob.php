<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Jobs;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesEventFactory;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesWebhookCall;
use Spatie\Mailcoach\Mailcoach;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    use UsesMailcoachModels;

    public function __construct(SesWebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        $this->queue = config('mailcoach.campaigns.perform_on_queue.process_feedback_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle(): void
    {
        if (! $this->validateMessageFromWebhookCall()) {
            $this->webhookCall->delete();

            return;
        }

        if (! $this->webhookCall->isFirstOfThisSesMessage()) {
            $this->webhookCall->delete();

            return;
        }

        $payload = json_decode($this->webhookCall->payload['Message'], true);

        $send = $this->getSendByMessageHeader($payload) ?? $this->getSendByMessageId($payload);

        if (! $send) {
            $this->markAsProcessed();

            return;
        }

        if (! Arr::get($payload, 'eventType')) {
            $this->markAsProcessed();

            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($send);

        $this->markAsProcessed();
    }

    protected function markAsProcessed(): void
    {
        event(new WebhookCallProcessedEvent($this->webhookCall));
    }

    protected function validateMessageFromWebhookCall(): bool
    {
        $validator = resolve(MessageValidator::class);

        try {
            $message = Message::fromJsonString(json_encode($this->webhookCall->payload));
        } catch (Exception) {
            return false;
        }

        return $validator->isValid($message);
    }

    protected function getSendByMessageHeader(?array $payload): ?Send
    {
        if (! $payload) {
            return null;
        }

        $headers = Arr::get($payload, 'mail.headers', []);

        foreach ($headers as $header) {
            if ($header['name'] === 'Message-ID') {
                $messageId = (string) str($header['value'])->between('<', '>');

                return self::getSendClass()::findByTransportMessageId($messageId);
            }
        }

        return null;
    }

    protected function getSendByMessageId(?array $payload): ?Send
    {
        if (! $payload) {
            return null;
        }

        if (! $messageId = $payload['mail']['messageId'] ?? null) {
            return null;
        }

        return self::getSendClass()::findByTransportMessageId($messageId);
    }
}
