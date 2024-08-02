<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagSegmentRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        $emailListTagNames = $this->route('emailList')->tags()->pluck('name')->toArray();

        return [
            'name' => ['required'],
            'all_positive_tags_required' => ['boolean'],
            'all_negative_tags_required' => ['boolean'],
            'positive_tags' => ['nullable', 'array'],
            'positive_tags.*' => [Rule::in($emailListTagNames)],
            'negative_tags' => ['nullable', 'array'],
            'negative_tags.*' => [Rule::in($emailListTagNames)],
        ];
    }
}
