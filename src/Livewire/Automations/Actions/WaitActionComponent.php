<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Enums\WaitUnit;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\GetIntervalAction;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;
use Throwable;

class WaitActionComponent extends AutomationActionComponent
{
    public ?string $length = '1';

    public ?string $unit;

    public array $units = [];

    public function mount()
    {
        $this->units = WaitUnit::options();
        $this->length ??= '1';
        $this->unit ??= WaitUnit::Days->value;
        $this->unit = Str::plural($this->unit);
    }

    public function getData(): array
    {
        return [
            'seconds' => app(GetIntervalAction::class)->execute($this->length, $this->unit)->totalSeconds,
            'unit' => $this->unit,
            'length' => $this->length,
        ];
    }

    public function getDescriptionProperty(): string
    {
        if (! $this->length || ! $this->unit) {
            return '…';
        }

        if ($this->length <= 30) {
            return $this->length.' '.Str::plural(Str::singular($this->unit), $this->length);
        }

        try {
            $interval = CarbonInterval::{$this->unit}($this->length);

            return $interval?->cascade()->forHumans() ?? '…';
        } catch (Throwable) {
            return '…';
        }
    }

    public function rules(): array
    {
        return [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(WaitUnit::cases())],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.waitAction');
    }
}
