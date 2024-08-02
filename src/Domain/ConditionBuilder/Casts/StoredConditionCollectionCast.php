<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

class StoredConditionCollectionCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return StoredConditionCollection::make();
        }

        $data = collect(json_decode($value, true, flags: JSON_THROW_ON_ERROR))
            ->map(function (array $item) {
                return StoredCondition::make(
                    $item['condition_key'],
                    $item['comparison_operator'],
                    $item['value'],
                );
            });

        return StoredConditionCollection::make($data);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Collection) {
            $value = $value->all();
        }

        if (! is_array($value)) {
            throw ConditionException::cannotCast($model::class, $key);
        }

        return collect($value)
            ->map(function (StoredCondition|array $item) {
                if ($item instanceof StoredCondition) {
                    return $item->toDb();
                }

                return $item;
            })->toJson();
    }
}
