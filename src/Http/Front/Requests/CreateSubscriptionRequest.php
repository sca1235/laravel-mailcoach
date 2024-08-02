<?php

namespace Spatie\Mailcoach\Http\Front\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateSubscriptionRequest extends FormRequest
{
    use UsesMailcoachModels;

    private ?EmailList $emailList = null;

    public function rules()
    {
        return [
            'email' => ['required', 'email:strict,dns'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'redirect_after_subscribed' => 'nullable',
            'redirect_after_already_subscribed' => 'nullable',
            'redirect_after_subscription_pending' => 'nullable',
            'tags' => 'nullable',
            'attributes' => 'nullable',
        ];
    }

    public function subscriberAttributes(): array
    {
        return Arr::except($this->validated(), [
            'email',
            'redirect_after_subscribed',
            'redirect_after_already_subscribed',
            'redirect_after_subscription_pending',
            'tags',
            'attributes',
        ]);
    }

    public function attributes()
    {
        $allowedEmailListAttributes = $this->emailList()->allowedFormExtraAttributes();

        $attributes = [];

        foreach ($this->get('attributes', []) as $key => $attributeValue) {
            if (in_array(trim($key), $allowedEmailListAttributes)) {
                $attributes[$key] = $attributeValue;
            }
        }

        return $attributes;
    }

    public function tags(): array
    {
        $tags = explode(';', $this->tags);

        $tags = array_map('trim', $tags);

        $allowedEmailListTags = $this->emailList()->allowedFormSubscriptionTags()->pluck('name')->toArray();

        $tags = array_filter($tags, fn (string $tag) => in_array($tag, $allowedEmailListTags));

        return array_filter($tags);
    }

    public function emailList(): EmailList
    {
        if (! $this->emailList) {
            $this->emailList = self::getEmailListClass()::query()
                ->where('uuid', $this->route()->parameter('emailListUuid'))
                ->where('allow_form_subscriptions', true)
                ->firstOrFail();
        }

        return $this->emailList;
    }

    public function requiresTurnstile(): bool
    {
        return ! empty(config('mailcoach.turnstile_secret'));
    }

    public function hasTurnstileResponse(): bool
    {
        return $this->has('cf-turnstile-response');
    }

    public function validateTurnstile(): void
    {
        Validator::make([
            'cf-turnstile-response' => $this->get('cf-turnstile-response'),
        ], [
            'cf-turnstile-response' => ['required', function (string $attribute, mixed $value, Closure $fail) {
                if (! app()->environment('production')) {
                    return;
                }

                $success = Http::post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('mailcoach.turnstile_secret'),
                    'response' => $value,
                    'remoteip' => $this->ip(),
                ])->json('success');

                if (! $success) {
                    $fail(__mc('The turnstile validation failed.'));
                }
            }],
        ])->validate();
    }
}
