<?php

namespace Spatie\Mailcoach\Domain\Content\Listeners;

use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;

class AddOpenedTag
{
    public function handle(ContentOpenedEvent $event)
    {
        $contentItem = $event->open->contentItem;

        if (! $contentItem?->add_subscriber_tags) {
            return;
        }

        $subscriber = $event->open->subscriber;
        $type = match ($event->open->contentItem->model_type) {
            'automation_mail' => 'automation-mail',
            default => $event->open->contentItem->model_type,
        };

        $subscriber->addTag("{$type}-{$contentItem->model->uuid}-opened", TagType::Mailcoach);
    }
}
