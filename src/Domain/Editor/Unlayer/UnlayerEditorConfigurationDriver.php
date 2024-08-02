<?php

namespace Spatie\Mailcoach\Domain\Editor\Unlayer;

use Illuminate\Contracts\Config\Repository;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class UnlayerEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Unlayer';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public function validationRules(): array
    {
        return [
            'project_id' => ['nullable', 'string'],
        ];
    }

    public function defaults(): array
    {
        return [
            'project_id' => '',
        ];
    }

    public function registerConfigValues(Repository $config, array $values): void
    {
        parent::registerConfigValues($config, $values);

        config()->set('mailcoach.unlayer.options.projectId', $values['project_id'] ?? null);
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.unlayer';
    }
}
