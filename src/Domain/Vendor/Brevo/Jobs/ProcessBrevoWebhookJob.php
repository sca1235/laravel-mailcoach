<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Jobs;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Brevo\BrevoEventFactory;
use Spatie\Mailcoach\Mailcoach;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessBrevoWebhookJob extends ProcessWebhookJob
{
    use UsesMailcoachModels;

    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        $this->queue = config('mailcoach.campaigns.perform_on_queue.process_feedback_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle(): void
    {
        $payload = $this->webhookCall->payload;

        if ($send = $this->getSend()) {
            $brevoEvent = BrevoEventFactory::createForPayload($payload);
            $brevoEvent->handle($send);
        }

        event(new WebhookCallProcessedEvent($this->webhookCall));
    }

    protected function getSend(): ?Send
    {
        $messageId = Arr::get($this->webhookCall->payload, 'message-id');

        if (! $messageId) {
            return null;
        }

        $messageId = ltrim($messageId, '<');
        $messageId = rtrim($messageId, '>');

        /** @var class-string<Send> $sendClass */
        $sendClass = self::getSendClass();

        return $sendClass::findByTransportMessageId($messageId);
    }
}
