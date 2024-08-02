<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Jobs;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Resend\ResendEventFactory;
use Spatie\Mailcoach\Mailcoach;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessResendWebhookJob extends ProcessWebhookJob
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
            $resendEvent = ResendEventFactory::createForPayload($payload);
            $resendEvent->handle($send);
        }

        event(new WebhookCallProcessedEvent($this->webhookCall));
    }

    protected function getSend(): ?Send
    {
        $headers = Arr::get($this->webhookCall->payload, 'data.headers');
        $header = collect($headers)->first(function ($header) {
            return strtolower($header['name']) === 'mailcoach-send-uuid';
        });
        $uuid = $header['value'] ?? null;

        if (! $uuid) {
            return null;
        }

        /** @var class-string<Send> $sendClass */
        $sendClass = self::getSendClass();

        return $sendClass::findByUuid($uuid);
    }
}
