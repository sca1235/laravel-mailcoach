<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Automation\Enums\WaitUnit;

/** https://github.com/briannesbitt/Carbon/issues/2855 */
class GetIntervalAction
{
    public function execute(int $length, string $unit): CarbonInterval
    {
        if ($unit !== WaitUnit::Weekdays->value) {
            return CarbonInterval::$unit($length);
        }

        $fullWeeks = (int) floor($length / 7);
        $weekendDaysInInterval = $fullWeeks * 2;

        for ($i = 1; $i <= ($length % 7); $i++) {
            if ((new CarbonImmutable())->addDays($fullWeeks * 7 + $i)->isWeekend()) {
                $weekendDaysInInterval++;
            }

            if ($i === ($length % 7)
                && (new CarbonImmutable())->addDays($fullWeeks * 7 + $i)->isSaturday()) {
                $weekendDaysInInterval++;
            }
        }

        return CarbonInterval::days($length + $weekendDaysInInterval);
    }
}
