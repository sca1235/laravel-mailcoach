<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Editor\Markdown\Editor as MarkdownEditor;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\Template\Support\TemplateRenderer;
use Spatie\Mailcoach\Mailcoach;

class UpdateCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, array $attributes, ?Template $template = null): Campaign
    {
        $segment = null;

        if ($attributes['segment_id'] ?? null) {
            $segment = $attributes['segment_id'] ?? null
                ? self::getTagSegmentClass()::find($attributes['segment_id'])
                : null;
        }

        if ($attributes['segment_uuid'] ?? null) {
            $segment = $attributes['segment_uuid'] ?? null
                ? self::getTagSegmentClass()::findByUuid($attributes['segment_uuid'])
                : null;
        }

        /** @var ?TagSegment $segment */
        if (is_null($segment)) {
            $segmentClass = $attributes['segment_class'] ?? EverySubscriberSegment::class;
            $segmentDescription = (new $segmentClass)->description();
        } else {
            $segmentClass = SubscribersWithTagsSegment::class;
            $segmentDescription = $segment->description($campaign);
        }

        if (isset($attributes['email_list_uuid'])) {
            $attributes['email_list_id'] = self::getEmailListClass()::findByUuid($attributes['email_list_uuid'])->id;
        }

        $campaign->fill([
            'name' => $attributes['name'],
            'status' => CampaignStatus::Draft,
            'email_list_id' => $attributes['email_list_id'] ?? self::getEmailListClass()::orderBy('name')->first()?->id,
            'segment_id' => $segment?->id,
            'segment_class' => $segmentClass,
            'segment_description' => $segmentDescription,
            'scheduled_at' => $attributes['schedule_at'] ?? null,
            'disable_webview' => $attributes['disable_webview'] ?? false,
        ]);

        $campaign->save();

        $content = $campaign->contentItem;

        $html = $attributes['html'] ?? $template?->html;

        if ($template && $template->exists && isset($attributes['fields'])) {
            $fieldValues = [];

            foreach ($template->fields() as $field) {
                if ($field['type'] !== 'editor') {
                    $fieldValues[$field['name']] = Arr::get($attributes, "fields.{$field['name']}");

                    continue;
                }

                if (config('mailcoach.content_editor') === MarkdownEditor::class) {
                    $markdown = Arr::get($attributes, "fields.{$field['name']}") ?? '';

                    $fieldValues[$field['name']]['markdown'] = $markdown;
                    $fieldValues[$field['name']]['html'] = (string) app(RenderMarkdownToHtmlAction::class)->execute($markdown);
                }
            }

            $content->setTemplateFieldValues($fieldValues);
            $templateRenderer = (new TemplateRenderer($template->html ?? ''));
            $html = $templateRenderer->render($fieldValues);
        } elseif ($template && $template->exists) {
            $content->structured_html = $template->getStructuredHtml();
        } else {
            $content->setTemplateFieldValues([
                'html' => $html,
            ]);
        }

        if (containsMjml($html)) {
            $mjml = Mailcoach::getSharedActionClass('initialize_mjml', InitializeMjmlAction::class)->execute();
            $html = $mjml->toHtml($html);
        }

        $content->fill([
            'template_id' => $template?->id,
            'utm_tags' => $attributes['utm_tags'] ?? config('mailcoach.campaigns.default_settings.utm_tags', false),
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $html,
        ]);

        $content->save();

        return $campaign->refresh();
    }
}
