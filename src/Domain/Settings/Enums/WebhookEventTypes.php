<?php

namespace Spatie\Mailcoach\Domain\Settings\Enums;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Shared\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;

enum WebhookEventTypes
{
    case Subscribed;
    case UnconfirmedSubscriberCreated;
    case Unsubscribed;
    case CampaignSent;
    case TagAdded;
    case TagRemoved;
    case ContentOpened;
    case LinkClicked;
    case BounceRegistered;
    case SoftBounceRegistered;

    public function label(): string
    {
        return match ($this) {
            self::Subscribed => 'Subscribed',
            self::UnconfirmedSubscriberCreated => 'Unconfirmed subscriber created',
            self::Unsubscribed => 'Unsubscribed',
            self::CampaignSent => 'Campaign sent',
            self::TagAdded => 'Tag added',
            self::TagRemoved => 'Tag removed',
            self::ContentOpened => 'Email opened',
            self::LinkClicked => 'Link clicked',
            self::BounceRegistered => 'Bounced',
            self::SoftBounceRegistered => 'Soft bounced',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::Subscribed => class_basename(SubscribedEvent::class),
            self::UnconfirmedSubscriberCreated => class_basename(UnconfirmedSubscriberCreatedEvent::class),
            self::Unsubscribed => class_basename(UnsubscribedEvent::class),
            self::CampaignSent => class_basename(CampaignSentEvent::class),
            self::TagAdded => class_basename(TagAddedEvent::class),
            self::TagRemoved => class_basename(TagRemovedEvent::class),
            self::ContentOpened => class_basename(ContentOpenedEvent::class),
            self::LinkClicked => class_basename(LinkClickedEvent::class),
            self::BounceRegistered => class_basename(BounceRegisteredEvent::class),
            self::SoftBounceRegistered => class_basename(SoftBounceRegisteredEvent::class),
        };
    }
}
