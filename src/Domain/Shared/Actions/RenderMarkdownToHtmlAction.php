<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Tempest\Highlight\CommonMark\CodeBlockRenderer;
use Tempest\Highlight\CommonMark\InlineCodeBlockRenderer;

class RenderMarkdownToHtmlAction
{
    public function __construct(private MarkdownRenderer $renderer)
    {
        $this->renderer
            ->disableAnchors()
            ->addExtension(new TableExtension())
            ->addExtension(new StrikethroughExtension())
            ->addExtension(new AutolinkExtension());
    }

    public function execute(string $markdown, ?string $theme = null): HtmlString
    {
        /**
         * When Sidecar Shiki is configured and set up, we want to highlight through
         * that function instead of calling Shiki through node directly.
         */
        $renderer = clone $this->renderer;

        $renderer->highlightCode(false)
            ->addBlockRenderer(FencedCode::class, new CodeBlockRenderer())
            ->addInlineRenderer(Code::class, new InlineCodeBlockRenderer());

        $replacements = [
            '{{ ' => '@'.urlencode('{{ '),
            '{{' => '@'.urlencode('{{'),
            ' }}' => '@'.urlencode(' }}'),
            '}}' => '@'.urlencode('}}'),
        ];

        $markdown = str_replace(array_keys($replacements), array_values($replacements), $markdown);

        try {
            $html = $renderer
                ->highlightTheme($theme ?? 'nord')
                ->toHtml($markdown);
        } catch (\Throwable $e) {
            report($e);
            $html = $this->renderer
                ->highlightCode(false)
                ->toHtml($markdown);
        }

        $html = str_replace(array_values($replacements), array_keys($replacements), $html);

        return new HtmlString($html);
    }
}
