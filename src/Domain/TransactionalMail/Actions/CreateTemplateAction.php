<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes, ?Template $template = null)
    {
        $html = $attributes['body'] ?? $template?->html;
        $structured_html = $template?->getStructuredHtml();

        $mail = self::getTransactionalMailClass()::create([
            'name' => $attributes['name'],
            'type' => $attributes['type'],
        ]);

        $mail->contentItem->update([
            'template_id' => $template?->id,
            'html' => $html,
            'structured_html' => $structured_html,
        ]);

        return $mail;
    }
}
