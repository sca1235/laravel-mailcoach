<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Mailcoach;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
    ) {
    }

    public function execute(ContentItem $contentItem): void
    {
        if (containsMjml($contentItem->getHtml())) {
            $mjml = Mailcoach::getSharedActionClass('initialize_mjml', InitializeMjmlAction::class)->execute();
            $contentItem->email_html = $mjml->toHtml($contentItem->getHtml());
        } else {
            $contentItem->email_html = $contentItem->htmlWithInlinedCss();
        }

        if (empty($contentItem->email_html)) {
            $contentItem->save();

            return;
        }

        if ($contentItem->utm_tags) {
            $contentItem->email_html = $this->addUtmTagsToHtmlAction->execute($contentItem->email_html, $contentItem);
        }

        $contentItem->email_html = mb_convert_encoding($contentItem->email_html, 'UTF-8');

        $contentItem->save();
    }
}
