<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

class QuoteRenderer extends Renderer
{
    public function render(): string
    {
        return <<<HTML
        <blockquote style="text-align: {$this->data['alignment']}">
            {$this->data['text']}<br>
            {$this->data['caption']}
        </blockquote>
        HTML;
    }
}
