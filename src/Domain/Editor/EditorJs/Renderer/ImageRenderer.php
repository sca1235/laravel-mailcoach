<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

class ImageRenderer extends Renderer
{
    public function render(): string
    {
        // @todo withBorder, withBackground, stretched options
        return <<<HTML
        <div>
            <img src="{$this->data['file']['url']}" alt="">
            <p>{$this->data['caption']}</p>
        </div>
        HTML;
    }
}
