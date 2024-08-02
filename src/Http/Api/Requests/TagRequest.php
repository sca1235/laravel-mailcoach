<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'visible_in_preferences' => ['nullable', 'boolean'],
        ];
    }
}
