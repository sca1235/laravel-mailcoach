<?php

namespace Spatie\Mailcoach\Domain\Editor\Markdown;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class MarkdownEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Markdown';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public function validationRules(): array
    {
        return [
            'text_direction' => ['nullable', 'string', Rule::in('ltr', 'rtl')],
        ];
    }

    public function defaults(): array
    {
        return [
            'text_direction' => 'ltr',
        ];
    }

    public function registerConfigValues(Repository $config, array $values): void
    {
        parent::registerConfigValues($config, $values);

        config()->set('mailcoach.markdown.options.text_direction', $values['text_direction'] ?? 'ltr');
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.markdown';
    }
}
