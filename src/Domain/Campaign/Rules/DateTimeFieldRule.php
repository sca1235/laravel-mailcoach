<?php

namespace Spatie\Mailcoach\Domain\Campaign\Rules;

use Carbon\CarbonInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;

class DateTimeFieldRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail(__mc('Invalid date time provided.'));

            return;
        }

        foreach (['date', 'hours', 'minutes'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $value)) {
                $message = [
                    'date' => __mc('Date key is missing'),
                    'hours' => __mc('Hours key is missing'),
                    'minutes' => __mc('Minutes key is missing'),
                ][$requiredKey];

                $fail($message);

                return;
            }
        }

        $dateTime = $this->parseDateTime($value);

        if (! $dateTime) {
            $fail(__mc('Invalid date time provided.'));

            return;
        }

        if (! $dateTime->isFuture()) {
            $fail(__mc('Date time must be in the future.'));
        }
    }

    public function parseDateTime(array $value): ?CarbonInterface
    {
        try {
            $hours = str_pad($value['hours'], 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($value['minutes'], 2, '0', STR_PAD_LEFT);

            /** @var CarbonInterface $dateTime */
            $dateTime = Date::createFromFormat(
                'Y-m-d H:i',
                "{$value['date']} {$hours}:{$minutes}",
                config('mailcoach.timezone') ?? config('app.timezone'),
            );

            return $dateTime;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
