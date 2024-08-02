<?php

namespace Spatie\Mailcoach\Domain\Settings\Support;

use DateTimeZone;

class TimeZone
{
    public static function all(): array
    {
        $timeZones = ['UTC', ...DateTimeZone::listIdentifiers()];

        return array_combine($timeZones, $timeZones);
    }
}
