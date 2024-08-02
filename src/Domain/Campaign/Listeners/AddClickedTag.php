<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Content\Support\LinkHasher;

class AddClickedTag
{
    public function handle(LinkClickedEvent $event)
    {
        $contentItem = $event->click->link->contentItem;

        if (! $contentItem) {
            return;
        }

        $subscriber = $event->click->subscriber;
        $type = match ($event->click->link->contentItem->model_type) {
            'automation_mail' => 'automation-mail',
            default => $event->click->link->contentItem->model_type,
        };

        if ($contentItem->add_subscriber_tags) {
            $subscriber->addTag("{$type}-{$contentItem->model->uuid}-clicked", TagType::Mailcoach);
        }

        if ($contentItem->add_subscriber_link_tags) {
            $hash = LinkHasher::hash(
                sendable: $contentItem->model,
                url: $event->click->link->url,
            );

            $subscriber->addTag($hash, TagType::Mailcoach);
        }
    }
}
