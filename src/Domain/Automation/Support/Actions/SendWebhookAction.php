<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;
use Spatie\WebhookServer\WebhookCall;

class SendWebhookAction extends AutomationAction
{
    use SerializesModels;
    use UsesMailcoachModels;

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::React;
    }

    public static function make(array $data): self
    {
        return new self($data['url'] ?? '', $data['secret'] ?? '');
    }

    public function __construct(public string $url, public string $secret)
    {
        parent::__construct();
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::send-webhook-action';
    }

    public static function getName(): string
    {
        return (string) __mc('Send a webhook');
    }

    public static function getIcon(): string
    {
        return 'heroicon-s-arrow-top-right-on-square';
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'secret' => $this->secret,
        ];
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        $payload = [
            'automation_name' => $actionSubscriber->action->automation->name,
            'automation_uuid' => $actionSubscriber->action->automation->uuid,
            'subscriber' => SubscriberResource::make($actionSubscriber->subscriber)->toArray(request()),
        ];

        WebhookCall::create()
            ->onQueue(config('mailcoach.perform_on_queue.send_webhooks'))
            ->timeoutInSeconds(10)
            ->maximumTries(5)
            ->url($this->url)
            ->payload($payload)
            ->useSecret($this->secret)
            ->throwExceptionOnFailure()
            ->dispatch();
    }
}
