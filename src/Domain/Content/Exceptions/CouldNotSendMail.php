<?php

namespace Spatie\Mailcoach\Domain\Content\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class CouldNotSendMail extends Exception
{
    public static function invalidContent(ContentItem $contentItem, Exception $errorException): self
    {
        return new static("The {$contentItem->model_type} with id `{$contentItem->model_id}` can't be sent because the content isn't valid. Please check if the html is valid. DOMDocument reported: `{$errorException->getMessage()}`", 0, $errorException);
    }

    public static function invalidMailableClass(ContentItem $contentItem, string $invalidMailableClass): self
    {
        $mustExtend = MailcoachMail::class;

        return new static("The {$contentItem->model_type} with id `{$contentItem->model_id}` can't be sent, because an invalid mailable class `{$invalidMailableClass}` is set. A valid mailable class must extend `{$mustExtend}`.");
    }
}
