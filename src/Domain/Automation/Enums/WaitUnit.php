<?php

namespace Spatie\Mailcoach\Domain\Automation\Enums;

enum WaitUnit: string
{
    case Minutes = 'minutes';
    case Hours = 'hours';
    case Days = 'days';
    case Weekdays = 'weekdays';
    case Weeks = 'weeks';
    case Months = 'months';

    public function label(): string
    {
        return match ($this) {
            self::Minutes => __mc('Minute'),
            self::Hours => __mc('Hour'),
            self::Days => __mc('Day'),
            self::Weekdays => __mc('Weekday'),
            self::Weeks => __mc('Week'),
            self::Months => __mc('Month'),
        };
    }

    public static function options(): array
    {
        return [
            self::Minutes->value => __mc('Minute'),
            self::Hours->value => __mc('Hour'),
            self::Days->value => __mc('Day'),
            self::Weekdays->value => __mc('Weekday'),
            self::Weeks->value => __mc('Week'),
            self::Months->value => __mc('Month'),
        ];
    }
}
