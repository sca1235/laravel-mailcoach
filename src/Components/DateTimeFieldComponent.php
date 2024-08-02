<?php

namespace Spatie\Mailcoach\Components;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class DateTimeFieldComponent extends Component
{
    public CarbonInterface $value;

    public function __construct(public string $name, ?CarbonInterface $value = null)
    {
        $this->value = $value ?? now()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->addHour()->startOfHour();
    }

    public function hourOptions(): Collection
    {
        return collect(range(0, 23))->mapWithKeys(function (int $hour) {
            return [$hour => str_pad((string) $hour, 2, '0', STR_PAD_LEFT)];
        });
    }

    public function minuteOptions(): Collection
    {
        return collect(range(0, 59, 5))->mapWithKeys(function (int $minutes) {
            return [$minutes => str_pad((string) $minutes, 2, '0', STR_PAD_LEFT)];
        });
    }

    public function render()
    {
        return view('mailcoach::app.components.form.dateTimeField');
    }
}
