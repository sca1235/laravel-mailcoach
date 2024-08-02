<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions;

use Spatie\Mailcoach\Domain\ConditionBuilder\Data\ConditionData;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;

interface Condition
{
    public function key(): string;

    public function label(): string;

    /** @return array<ComparisonOperator> */
    public function comparisonOperators(): array;

    public function category(): ConditionCategory;

    public function getComponent(): string;

    /** @return array{key: string, label: string, comparison_operators: array} */
    public function toArray(): array;

    /** @return class-string<ConditionData>|null */
    public function dto(): ?string;
}
