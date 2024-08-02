<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class AddUtmTagsToHtmlAction
{
    public function __construct(
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        protected AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(string $html, ContentItem $contentItem): string
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            if ($linkElement->hasAttribute('data-no-utm')) {
                continue;
            }

            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $contentItem);
            $linkElement->setAttribute('href', $newUrl);
        }

        return $document->saveHTML();
    }
}
