<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions;

use RuntimeException;

class ConditionException extends RuntimeException
{
    public static function cannotInstantiate(string $key): self
    {
        return new self("Unable to create a condition for key `{$key}`.");
    }

    public static function cannotCast(string $modelClass, string $attribute): self
    {
        return new self("Attribute `{$attribute}` of model `{$modelClass}` cannot be cast.");
    }

    public static function cannotUseOperator(string $operator, string $condition): self
    {
        return new self("Operator `{$operator}` is not allowed for condition `{$condition}`.");
    }

    public static function unsupportedValue(string $value): self
    {
        return new self("Value `{$value}` is not supported.");
    }
}
