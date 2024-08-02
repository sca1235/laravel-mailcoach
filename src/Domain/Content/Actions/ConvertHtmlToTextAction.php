<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Exception;
use League\HTMLToMarkdown\HtmlConverter;

class ConvertHtmlToTextAction
{
    public function execute(string $html): string
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'suppress_errors' => true,
            'remove_nodes' => 'head script style',
        ]);

        try {
            $text = $converter->convert($html);
        } catch (Exception) {
            $text = '';
        }

        return $text;
    }
}
