<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;

class CampaignRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        return [
            'name' => ['required'],
            'subject' => ['nullable'],
            'type' => ['nullable', Rule::in([CampaignStatus::Draft->value])],
            'email_list_uuid' => ['required', Rule::exists(self::getEmailListClass(), 'uuid')],
            'segment_uuid' => ['nullable', Rule::exists(self::getTagSegmentClass(), 'uuid')],
            'html' => ['nullable'],
            'fields' => ['nullable'],

            'mailable_class' => ['nullable'],
            'utm_tags' => ['nullable', 'boolean'],
            'disable_webview' => ['nullable', 'boolean'],
            'schedule_at' => ['date_format:Y-m-d H:i:s'],
        ];
    }

    public function template(): Template
    {
        $templateClass = self::getTemplateClass();

        return $templateClass::findByUuid($this->template_uuid) ?? new $templateClass();
    }
}
