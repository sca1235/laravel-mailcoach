<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class HasTagCondition implements Condition
{
    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __mc('Has tag');
    }

    public static function getDescription(array $data): string
    {
        $tag = is_array($data['tag']) ? $data['tag']['value'] : $data['tag'];

        return (string) __mc(':tag', ['tag' => $tag]);
    }

    public static function rules(): array
    {
        return [
            'tag' => 'required',
        ];
    }

    public function check(): bool
    {
        $this->subscriber->load('tags');

        $tag = is_array($this->data['tag'])
            ? $this->data['tag']['value']
            : $this->data['tag'];

        return $this->subscriber->hasTag($tag);
    }
}
