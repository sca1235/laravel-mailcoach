<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;

class StoredCondition
{
    protected function __construct(
        public readonly Condition $condition,
        public readonly ComparisonOperator $comparisonOperator,
        public readonly mixed $value
    ) {
    }

    public static function make(
        string|Condition $key,
        string|ComparisonOperator $comparisonOperator,
        mixed $value,
    ): self {
        return new self(
            condition: self::normalizeCondition($key),
            comparisonOperator: self::normalizeComparisonOperator($comparisonOperator),
            value: self::normalizeInput($value),
        );
    }

    public static function fromRequest(array $data): self
    {
        return self::make(
            key: $data['condition']['key'],
            comparisonOperator: $data['comparison_operator'] ?? ComparisonOperator::Equals,
            value: $data['value'],
        );
    }

    public static function fromDb(array $data): self
    {
        return self::make(
            key: $data['condition_key'],
            comparisonOperator: $data['comparison_operator'],
            value: $data['value'],
        );
    }

    public static function blueprint(Condition $condition): array
    {
        return [
            'condition' => $condition->toArray(),
            'comparison_operator' => null,
            'value' => [],
        ];
    }

    /**
     * @return array{condition: array{key: string, label: string, comparison_operators: array}, comparison_operator: string, value: mixed}
     */
    public function toArray(): array
    {
        return [
            'condition' => $this->condition->toArray(),
            'comparison_operator' => $this->comparisonOperator->value,
            'value' => $this->value,
        ];
    }

    public function toDb(): array
    {
        /** Be carefully with changes, stored in db */
        return [
            'condition_key' => $this->condition->key(),
            'comparison_operator' => $this->comparisonOperator->value,
            'value' => $this->value,
        ];
    }

    protected static function normalizeCondition(string|Condition $value): Condition
    {
        if ($value instanceof Condition) {
            return $value;
        }

        return app(CreateConditionFromKeyAction::class)->execute($value);
    }

    protected static function normalizeInput(mixed $value): mixed
    {
        if ($value instanceof Collection) {
            return $value->toArray();
        }

        return $value;
    }

    protected static function normalizeComparisonOperator(string|ComparisonOperator $value): ComparisonOperator
    {
        if ($value instanceof ComparisonOperator) {
            return $value;
        }

        return ComparisonOperator::fromName($value);
    }
}
