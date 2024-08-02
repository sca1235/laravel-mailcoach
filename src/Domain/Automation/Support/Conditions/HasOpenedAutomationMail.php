<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasOpenedAutomationMail implements Condition
{
    use UsesMailcoachModels;

    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __mc('Has opened automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || ! $data['automation_mail_id']) {
            return '';
        }

        $automationMail = static::getAutomationMailClass()::find($data['automation_mail_id']);

        if (! $automationMail) {
            return '';
        }

        return $automationMail->name;
    }

    public static function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailClass(), 'id'),
            ],
        ];
    }

    public function check(): bool
    {
        $mail = self::getAutomationMailClass()::find($this->data['automation_mail_id']);

        if (! $mail) {
            return false;
        }

        return self::getOpenClass()::query()
            ->where('subscriber_id', $this->subscriber->id)
            ->where('content_item_id', $mail->contentItem->id)
            ->exists();
    }
}
