<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Jobs;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\MailgunEventFactory;
use Spatie\Mailcoach\Mailcoach;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessMailgunWebhookJob extends ProcessWebhookJob
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
            $mailgunEvent = MailgunEventFactory::createForPayload($payload);
            $mailgunEvent->handle($send);
        }

        event(new WebhookCallProcessedEvent($this->webhookCall));
    }

    protected function getSend(): ?Send
    {
        $messageId = Arr::get($this->webhookCall->payload, 'event-data.message.headers.message-id');

        if (! $messageId) {
            return null;
        }

        /** @var class-string<Send> $sendClass */
        $sendClass = self::getSendClass();

        return $sendClass::findByTransportMessageId($messageId);
    }
}
