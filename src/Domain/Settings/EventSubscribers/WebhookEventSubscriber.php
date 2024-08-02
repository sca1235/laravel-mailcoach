<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction;
use Spatie\Mailcoach\Domain\Shared\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;
use Spatie\Mailcoach\Mailcoach;

class WebhookEventSubscriber
{
    public function subscribe(): array
    {
        return [
            SubscribedEvent::class => 'handleSubscribedEvent',
            UnconfirmedSubscriberCreatedEvent::class => 'handleUnconfirmedSubscriberCreatedEvent',
            UnsubscribedEvent::class => 'handleUnsubscribedEvent',
            CampaignSentEvent::class => 'handleCampaignSent',
            TagAddedEvent::class => 'handleTagAddedEvent',
            TagRemovedEvent::class => 'handleTagRemovedEvent',

            ContentOpenedEvent::class => 'handleContentOpenedEvent',
            LinkClickedEvent::class => 'handleLinkClickedEvent',
            BounceRegisteredEvent::class => 'handleBounceRegisteredEvent',
            SoftBounceRegisteredEvent::class => 'handleSoftBounceRegisteredEvent',
        ];
    }

    public function handleSubscribedEvent(SubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleUnsubscribedEvent(UnsubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleUnconfirmedSubscriberCreatedEvent(UnconfirmedSubscriberCreatedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleCampaignSent(CampaignSentEvent $event)
    {
        $emailList = $event->campaign->emailList;

        $payload = CampaignResource::make($event->campaign)->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleTagAddedEvent(TagAddedEvent $event)
    {
        $emailList = $event->subscriber->emailList;
        $tag = $event->tag;

        $payload = array_merge(
            SubscriberResource::make($event->subscriber)->toArray(request()),
            ['added_tag' => $tag->name]
        );

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleTagRemovedEvent(TagRemovedEvent $event)
    {
        $emailList = $event->subscriber->emailList;
        $tag = $event->tag;

        $payload = array_merge(
            SubscriberResource::make($event->subscriber)->toArray(request()),
            ['removed_tag' => $tag->name]
        );

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleContentOpenedEvent(ContentOpenedEvent $event)
    {
        if (! $model = $event->open->contentItem?->getModel()) {
            return;
        }

        if (! $emailList = $model->emailList) {
            return;
        }

        $payload = [
            'open_uuid' => $event->open->uuid,
            'send_uuid' => $event->open->send->uuid,
            'model_uuid' => $model->uuid,
            'model_type' => $model->getMorphClass(),
            'subscriber_uuid' => $event->open->subscriber?->uuid,
            'subscriber_email' => $event->open->subscriber?->email,
            'opened_at' => $event->open->created_at,
        ];

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleLinkClickedEvent(LinkClickedEvent $event)
    {
        if (! $emailList = $event->click->subscriber?->emailList) {
            return;
        }

        $payload = [
            'send_uuid' => $event->click->send->uuid,
            'click_uuid' => $event->click->uuid,
            'link_uuid' => $event->click->link->uuid,
            'email_list_uuid' => $event->click->subscriber->emailList->uuid,
            'subscriber_uuid' => $event->click->subscriber->uuid,
            'subscriber_email' => $event->click->subscriber->email,
            'clicked_at' => $event->click->created_at,
        ];

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleBounceRegisteredEvent(BounceRegisteredEvent $event)
    {
        if (! $emailList = $event->send->subscriber?->emailList) {
            return;
        }

        $payload = [
            'send_uuid' => $event->send->uuid,
            'subscriber_uuid' => $event->send->subscriber->uuid,
            'subscriber_email' => $event->send->subscriber->email,
            'email_list_uuid' => $event->send->subscriber->emailList->uuid,
        ];

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleSoftBounceRegisteredEvent(SoftBounceRegisteredEvent $event)
    {
        if (! $emailList = $event->send->subscriber?->emailList) {
            return;
        }

        $payload = [
            'send_uuid' => $event->send->uuid,
            'subscriber_uuid' => $event->send->subscriber->uuid,
            'subscriber_email' => $event->send->subscriber->email,
            'email_list_uuid' => $event->send->subscriber->emailList->uuid,
        ];

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    protected function sendWebhookAction(): SendWebhookAction
    {
        /** @var SendWebhookAction $action */
        $action = Mailcoach::getSharedActionClass('send_webhook', SendWebhookAction::class);

        return $action;
    }
}
