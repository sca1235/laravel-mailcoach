<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;

class UpdateAutomationMailAction
{
    use UsesMailcoachModels;

    public function execute(AutomationMail $automationMail, array $attributes, ?Template $template = null): AutomationMail
    {
        $html = $attributes['html'] ?? $template?->html;

        if ($template && $template->exists) {
            $automationMail->contentItem->structured_html = $template->getStructuredHtml();
        } else {
            $automationMail->contentItem->setTemplateFieldValues([
                'html' => $html,
            ]);
        }

        $automationMail->fill([
            'name' => $attributes['name'],
        ]);

        $automationMail->save();

        $automationMail->contentItem->update([
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $html,
            'template_id' => $template?->id,
            'utm_tags' => $attributes['utm_tags'] ?? config('mailcoach.automation.default_settings.utm_tags', false),
        ]);

        return $automationMail->refresh();
    }
}
