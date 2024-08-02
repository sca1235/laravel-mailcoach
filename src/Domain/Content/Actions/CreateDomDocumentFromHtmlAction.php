<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use DOMDocument;

class CreateDomDocumentFromHtmlAction
{
    public function execute(string $html, bool $suppressErrors = true): DOMDocument
    {
        $html = preg_replace('/&(?![^& ]+;)/', '&amp;', $html);

        $html = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, ~0], 'UTF-8');

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors($suppressErrors);
        $document->loadHTML($html);
        libxml_use_internal_errors($internalErrors);
        $document->formatOutput = true;

        return $document;
    }
}
