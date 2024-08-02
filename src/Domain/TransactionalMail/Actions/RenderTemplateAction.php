<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Mailcoach;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Throwable;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class RenderTemplateAction
{
    public function __construct(private InitializeMjmlAction $initializeMjmlAction)
    {
    }

    public function execute(
        TransactionalMail $template,
        Mailable $mailable,
        array $replacements = [],
    ) {
        $html = $template->contentItem->html ?? '';

        if (containsMjml($html)) {
            $mjml = $this->initializeMjmlAction->execute();
            $html = $mjml->toHtml($html);
        }

        $html = $this->renderTemplateBody($template, $html, $mailable);

        $html = $this->handleReplacements($html, $replacements);

        $html = $this->executeReplacers($html, $template, $mailable);

        if ($template->type === 'html') {
            try {
                $html = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class)->execute($html, Arr::undot($replacements));
            } catch (Throwable) {
                // Do nothing, Twig failed
            }
        }

        if (empty($html)) {
            return '';
        }

        return (new CssToInlineStyles())->convert($html);
    }

    protected function renderTemplateBody(
        TransactionalMail $template,
        string $body,
        Mailable $mailable,
    ): string {
        return match ($template->type) {
            'blade' => Blade::render($body, $mailable->buildViewData()),
            'markdown' => (string) app(RenderMarkdownToHtmlAction::class)->execute($body),
            'blade-markdown' => $this->compileBladeMarkdown(
                bladeString: $body,
                data: $mailable->buildViewData(),
                theme: $mailable->theme
            ),

            default => $body,
        };
    }

    protected function handleReplacements(string $body, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            if (is_array($replace)) {
                continue;
            }

            $body = str_replace("::{$search}::", $replace, $body);
        }

        return $body;
    }

    protected function executeReplacers(string $body, TransactionalMail $template, Mailable $mailable): string
    {
        foreach ($template->replacers() as $replacer) {
            $body = $replacer->replace($body, $mailable, $template);
        }

        return $body;
    }

    protected function compileBladeMarkdown(string $bladeString, array $data, ?string $theme = null): string
    {
        $tempDir = (new TemporaryDirectory())->create();
        $path = $tempDir->path('temporary-template-view.blade.php');

        File::put($path, $bladeString);

        $viewFactory = app(Factory::class);
        $viewFactory->addLocation($tempDir->path());

        $html = app(Markdown::class)
            ->theme($theme ?? 'default')
            ->render('temporary-template-view', $data)
            ->toHtml();

        $tempDir->delete();

        return $html;
    }
}
