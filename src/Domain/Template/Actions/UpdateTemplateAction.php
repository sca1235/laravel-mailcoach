<?php

namespace Spatie\Mailcoach\Domain\Template\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;

class UpdateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(Template $template, array $attributes): Template
    {
        $template->update([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);

        return $template->refresh();
    }
}
