<?php

namespace Spatie\Mailcoach\Domain\Editor\EditorJs;

use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class EditorJsEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Editor.js';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.editor';
    }
}
