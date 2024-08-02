<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StoredConditionRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The given value is not an array.');

            return;
        }

        if (! isset($value['condition'])) {
            $fail('The given value does not contain a condition.');

            return;
        }

        if (! isset($value['comparison_operator'])) {
            $fail('The given value does not contain a comparison type.');

            return;
        }

        if (! isset($value['value'])) {
            $fail('The given value does not contain a value.');

            return;
        }

        if (! is_string($value['condition']['key'])) {
            $fail('The given value does not contain a valid condition key.');

            return;
        }

        if (! is_string($value['comparison_operator'])) {
            $fail('The given value does not contain a valid comparison type.');

            return;
        }
    }
}
