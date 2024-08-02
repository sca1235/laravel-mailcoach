<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Collections;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Casts\StoredConditionCollectionCast;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberTagsQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

class StoredConditionCollection extends Collection implements Castable
{
    public static function castUsing(array $arguments): string
    {
        return StoredConditionCollectionCast::class;
    }

    public static function fromRequest(array $data): self
    {
        return collect($data)
            ->map(fn (array $condition) => StoredCondition::fromRequest($condition))
            ->pipe(fn (Collection $collection) => new self($collection));
    }

    // @todo does not work when named toArray()
    public function castToArray(): array
    {
        // @todo better way to do this?
        return $this->map(fn (StoredCondition $condition) => $condition->toArray())->toArray();
    }

    public function addSubscriberTags(mixed $value, ?ComparisonOperator $operator = null): self
    {
        $operator ??= ComparisonOperator::In;

        $storedCondition = StoredCondition::make(
            key: SubscriberTagsQueryCondition::KEY,
            comparisonOperator: $operator,
            value: $value
        );

        $this->ensureOperatorIsSupported($storedCondition, $operator);

        return $this->add($storedCondition);
    }

    private function ensureOperatorIsSupported(StoredCondition $storedCondition, ComparisonOperator $operator): void
    {
        if (! in_array($operator, $storedCondition->condition->comparisonOperators())) {
            throw ConditionException::cannotUseOperator($operator->value, $storedCondition->condition->label());
        }
    }
}
