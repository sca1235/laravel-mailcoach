<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $this->addCampaignCheck($validator);
        });
    }

    public function addCampaignCheck(Validator $validator)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $campaign = $this->route('campaign');

        if (! $campaign->isDraft()) {
            $validator->errors()->add('campaign', 'You cannot send a campaign that already was sent.');
        }
    }
}
