<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Carbon\CarbonInterface;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;

class DateTrigger extends AutomationTrigger implements TriggeredBySchedule
{
    public function __construct(
        public CarbonInterface $date,
        public string $repeat = ''
    ) {
        parent::__construct();
    }

    public static function getName(): string
    {
        return (string) __mc('On a date');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::date-trigger';
    }

    public static function make(array $data): self
    {
        return new self(
            (new DateTimeFieldRule())->parseDateTime($data['date']),
            $data['repeat'] ?? '',
        );
    }

    public static function rules(): array
    {
        return [
            'date' => ['required', new DateTimeFieldRule()],
            'repeat' => ['nullable', Rule::in(['daily', 'monthly', 'yearly'])],
        ];
    }

    public function trigger(): void
    {
        $now = now()->setTimezone($this->date->timezone);

        if ($now->lt($this->date)) {
            return;
        }

        if (isset($this->repeat)) {
            $date = match ($this->repeat) {
                'daily' => $this->date->setYear($now->year)->setMonth($now->month)->setDay($now->day),
                'monthly' => $this->date->setYear($now->year)->setMonth($now->month),
                'yearly' => $this->date->setYear($now->year),
                default => $this->date,
            };
        } else {
            $date = $this->date;
        }

        if ($this->automation->last_ran_at && $this->automation->last_ran_at->gt($date)) {
            return;
        }

        $this->runAutomation($this->automation->newSubscribersQuery());
    }
}
