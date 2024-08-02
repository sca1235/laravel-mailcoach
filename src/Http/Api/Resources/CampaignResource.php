<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
class CampaignResource extends JsonResource
{
    public function toArray($request)
    {
        $fields = collect($this->contentItem->getTemplateFieldValues())->map(function ($field) {
            return $field['markdown'] ?? $field; // If we have markdown content, only return the markdown
        })->toArray();

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,

            'email_list_uuid' => $this->emailList?->uuid,
            'email_list' => $this->whenLoaded('emailList', function () {
                return $this->emailList ? new EmailListResource($this->emailList) : null;
            }),

            'template_uuid' => $this->contentItem->template?->uuid,
            'template' => $this->contentItem->template_id ? $this->whenLoaded('template', function () {
                return new TemplateResource($this->contentItem->template);
            }) : null,

            'from_email' => $this->contentItem->from_email,
            'from_name' => $this->contentItem->subject,

            'status' => $this->status,

            'html' => $this->contentItem->html,
            'structured_html' => $this->contentItem->structured_html,
            'email_html' => $this->contentItem->email_html,
            'webview_html' => $this->contentItem->webview_html,

            'fields' => $fields,

            'mailable_class' => $this->contentItem->mailable_class,

            'utm_tags' => $this->contentItem->utm_tags,

            'sent_to_number_of_subscribers' => $this->contentItem->sent_to_number_of_subscribers,

            'segment_class' => $this->segment_class,
            'segment_description' => $this->segment_description,

            'open_count' => $this->contentItem->open_count,
            'unique_open_count' => $this->contentItem->unique_open_count,
            'open_rate' => $this->contentItem->open_rate,
            'click_count' => $this->contentItem->click_count,
            'unique_click_count' => $this->contentItem->unique_click_count,
            'click_rate' => $this->contentItem->click_rate,
            'unsubscribe_count' => $this->contentItem->unsubscribe_count,
            'unsubscribe_rate' => $this->contentItem->unsubscribe_rate,
            'bounce_count' => $this->contentItem->bounce_count,
            'bounce_rate' => $this->contentItem->bounce_rate,

            'sent_at' => $this->sent_at,
            'statistics_calculated_at' => $this->contentItem->statistics_calculated_at,
            'scheduled_at' => $this->scheduled_at,

            'summary_mail_sent_at' => $this->summary_mail_sent_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
