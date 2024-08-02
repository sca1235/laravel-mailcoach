<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Content\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasClickedAutomationMail implements Condition
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
        return (string) __mc('Has clicked automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || ! $data['automation_mail_id']) {
            return '';
        }

        $mail = static::getAutomationMailClass()::find($data['automation_mail_id']);

        return trim(vsprintf("%s \n %s", [
            $mail->name,
            isset($data['automation_mail_link_url']) && $data['automation_mail_link_url']
                ? $data['automation_mail_link_url']
                : '',
        ]));
    }

    public static function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailClass(), 'id'),
            ],
            'automation_mail_link_url' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function check(): bool
    {
        $mail = self::getAutomationMailClass()::find($this->data['automation_mail_id']);

        $query = static::getClickClass()::query()
            ->where('subscriber_id', $this->subscriber->id)
            ->whereHas('send', function (Builder $query) use ($mail) {
                $query->where('content_item_id', $mail->contentItem->id);
            });

        if ($this->data['automation_mail_link_url'] ?? false) {
            $url = $this->data['automation_mail_link_url'];

            if ($mail->contentItem->utm_tags) {
                $url = app(AddUtmTagsToUrlAction::class)->execute($url, $mail->contentItem);
            }

            $query->whereHas('link', function (Builder $query) use ($url) {
                $query->where('url', $url);
            });
        }

        return $query->exists();
    }
}
