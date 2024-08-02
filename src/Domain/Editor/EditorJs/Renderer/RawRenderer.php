<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

class RawRenderer extends Renderer
{
    public function render(): string
    {
        return <<<HTML
        {$this->data['html']}
        HTML;
    }
}
