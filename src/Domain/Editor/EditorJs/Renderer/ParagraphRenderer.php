<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

class ParagraphRenderer extends Renderer
{
    public function render(): string
    {
        return <<<HTML
        <p>{$this->data['text']}</p>
        HTML;
    }
}
