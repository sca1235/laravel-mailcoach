<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class EditorConfigurationDriverRepository
{
    /** @return Collection<EditorConfigurationDriver> */
    public function getSupportedEditors(): Collection
    {
        return collect(config('mailcoach.editors'))
            /** @var class-string<EditorConfigurationDriver> $editorConfigurationDriver */
            ->map(function (string $editorConfigurationDriver) {
                return resolve($editorConfigurationDriver);
            });
    }

    public function getForEditor(string $editorLabel): EditorConfigurationDriver
    {
        return $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $editorLabel === $editor->label())
            ?? $this->getSupportedEditors()->first();
    }

    public function getForClass(string $class): EditorConfigurationDriver
    {
        return $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $class === $editor->getClass())
            ?? $this->getSupportedEditors()->first();
    }
}
