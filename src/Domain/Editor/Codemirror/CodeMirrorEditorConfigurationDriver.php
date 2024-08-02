<?php

namespace Spatie\Mailcoach\Domain\Editor\Codemirror;

use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class CodeMirrorEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'CodeMirror';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.codemirror';
    }
}
