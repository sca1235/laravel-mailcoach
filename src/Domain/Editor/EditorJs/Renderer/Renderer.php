<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer;

abstract class Renderer
{
    public function __construct(protected array $data)
    {
    }

    abstract public function render(): string;
}
