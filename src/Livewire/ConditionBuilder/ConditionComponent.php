<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder;

use Livewire\Component;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class ConditionComponent extends Component
{
    use UsesMailcoachModels;

    public array $storedCondition;

    public int $index = 0;

    public string $title;

    public function mount(): void
    {
        $this->title = app(CreateConditionFromKeyAction::class)
            ->execute($this->storedCondition['condition']['key'])
            ->label();
    }

    public function getValue(): mixed
    {
        return $this->storedCondition['value'];
    }

    public function updated(): void
    {
        $this->storedCondition['value'] = $this->getValue();

        $this->dispatch('storedConditionUpdated', $this->index, $this->storedCondition);
    }

    public function delete(): void
    {
        $this->dispatch('storedConditionDeleted', $this->index);
    }
}
