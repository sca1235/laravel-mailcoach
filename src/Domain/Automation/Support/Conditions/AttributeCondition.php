<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class AttributeCondition implements Condition
{
    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getComparisons(): array
    {
        return [
            '=' => '=',
            '!=' => '≠',
            '>' => '>',
            '>=' => '≥',
            '<' => '<',
            '<=' => '≤',
            'empty' => __mc('is empty'),
            'not_empty' => __mc('is not empty'),
        ];
    }

    public static function getName(): string
    {
        return __mc('Attribute');
    }

    public static function getDescription(array $data): string
    {
        return __mc(':attribute :comparison :value', [
            'attribute' => $data['attribute'],
            'comparison' => self::getComparisons()[$data['comparison']] ?? null,
            'value' => $data['value'],
        ]);
    }

    public static function rules(array $conditionData): array
    {
        return [
            'attribute' => ['required', 'string'],
            'comparison' => ['required', Rule::in(array_keys(self::getComparisons()))],
            'value' => [Rule::requiredIf(function () use ($conditionData) {
                return ! in_array($conditionData['comparison'] ?? '', ['empty', 'not_empty']);
            }), 'nullable', 'string'],
        ];
    }

    public function check(): bool
    {
        $attribute = $this->data['attribute'];
        $attributeValue = $this->subscriber->extra_attributes->$attribute;

        if ($this->data['comparison'] === 'empty') {
            return (string) $attributeValue === '';
        }

        if ($this->data['comparison'] === 'not_empty') {
            return (string) $attributeValue !== '';
        }

        $value = $this->data['value'];

        try {
            $dateValue = Date::parse($value);
            $attributeDateValue = Date::parse($attributeValue);

            return match ($this->data['comparison']) {
                '=' => $attributeDateValue->eq($dateValue),
                '!=' => $attributeDateValue->notEqualTo($dateValue),
                '>' => $attributeDateValue->gt($dateValue),
                '>=' => $attributeDateValue->gte($dateValue),
                '<' => $attributeDateValue->lt($dateValue),
                '<=' => $attributeDateValue->lte($dateValue),
                default => false,
            };
        } catch (\Throwable) {
        }

        return match ($this->data['comparison']) {
            '=' => (string) $attributeValue === (string) $value,
            '!=' => (string) $attributeValue !== (string) $value,
            '>' => (int) $attributeValue > (int) $value,
            '>=' => (int) $attributeValue >= (int) $value,
            '<' => (int) $attributeValue < (int) $value,
            '<=' => (int) $attributeValue <= (int) $value,
            default => false,
        };
    }
}
