<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

class DelimiterRenderer extends Renderer
{
    public function render(): string
    {
        return <<<'HTML'
        <hr>
        HTML;
    }
}
