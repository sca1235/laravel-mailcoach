<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Actions;

use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\ConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class CreateConditionFromKeyAction
{
    public function execute(string $key): Condition
    {
        $result = collect(ConditionCollection::allConditions())
            ->first(function (Condition $condition) use ($key) {
                return $key === $condition->key();
            });

        if (! $result) {
            throw ConditionException::cannotInstantiate($key);
        }

        return $result;
    }
}
