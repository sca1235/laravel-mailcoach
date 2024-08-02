<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Traits;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

/** @mixin Condition */
trait Conditionable
{
    public function label(): string
    {
        return Str::headline(__mc($this->key()));
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key(),
            'label' => $this->label(),
            'comparison_operators' => $this->comparisonOperatorOptions(),
        ];
    }

    protected function ensureOperatorIsSupported(ComparisonOperator $operator): void
    {
        if (! in_array($operator, $this->comparisonOperators())) {
            throw ConditionException::cannotUseOperator($operator->value, $this->label());
        }
    }

    private function comparisonOperatorOptions(): array
    {
        return collect($this->comparisonOperators())
            ->flatMap(fn (ComparisonOperator $comparisonOperator) => $comparisonOperator->toOption())
            ->toArray();
    }
}
