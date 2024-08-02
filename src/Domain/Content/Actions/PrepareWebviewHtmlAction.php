<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class PrepareWebviewHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
    ) {
    }

    public function execute(ContentItem $contentItem): void
    {
        $model = $contentItem->getModel();

        if ($model instanceof Campaign && $model->disable_webview) {
            $contentItem->webview_html = null;
            $contentItem->save();

            return;
        }

        $contentItem->webview_html = $contentItem->htmlWithInlinedCss();
        $contentItem->webview_html = $this->replacePlaceholdersAction->execute($contentItem->webview_html, $model);

        if (empty(trim($contentItem->webview_html))) {
            $contentItem->save();

            return;
        }

        if ($contentItem->utm_tags) {
            $contentItem->webview_html = $this->addUtmTagsToHtmlAction->execute($contentItem->webview_html, $contentItem);
        }

        $webviewHtml = mb_convert_encoding($contentItem->webview_html, 'UTF-8');

        $webviewHtml = $this->removeHiddenTextFromWebview('<!-- webview:hide -->', '<!-- /webview:hide -->', $webviewHtml);
        $webviewHtml = $this->removeHiddenTextFromWebview('<!--webview:hide-->', '<!--/webview:hide-->', $webviewHtml);

        $contentItem->webview_html = $webviewHtml;
        $contentItem->save();
    }

    protected function removeHiddenTextFromWebview(string $beginTag, string $endTag, string $html): string
    {
        if (! str_contains($html, $beginTag)) {
            return $html;
        }

        $parts = explode($beginTag, $html);

        $matches = [];

        foreach ($parts as $part) {
            $matches[] = trim(explode($endTag, $part)[0]);
        }

        $matches = array_filter($matches);
        array_shift($matches);

        // Remove all hide comments
        return str_replace([$beginTag, $endTag, ...$matches], '', $html);
    }
}
