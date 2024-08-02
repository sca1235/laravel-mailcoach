<?php

namespace Spatie\Mailcoach\Http\Api\Requests\EmailLists\Subscribers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateSubscriberRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'email' => ['email:strict', $this->getUniqueRule()],
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string',
            'extra_attributes' => ['nullable', 'array'],
        ];
    }

    public function subscriberAttributes()
    {
        return Arr::except($this->validated(), ['tags']);
    }

    protected function getUniqueRule(): Unique
    {
        /** @var EmailList $emailList */
        $emailList = $this->route('emailList');

        /** @var string $subscriber */
        $subscriber = $this->route('subscriber');

        if (is_string($subscriber)) {
            /** @var Subscriber $subscriber */
            $subscriber = self::getSubscriberClass()::findOrFail($subscriber);
        }

        if (! $emailList) {
            $emailList = $subscriber->emailList;
        }

        $rule = Rule::unique(self::getSubscriberTableName(), 'email')->where('email_list_id', $emailList->id);

        $rule->ignore($subscriber->id);

        return $rule;
    }

    public function messages()
    {
        return [
            'email.unique' => 'There already is a subscriber on this list with this email.',
        ];
    }
}
