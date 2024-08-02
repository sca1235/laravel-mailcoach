<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberOpenedAutomationMailConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->options = self::getOpenClass()::query()
            ->with('contentItem.model')
            ->whereHas('contentItem', function ($query) {
                $query->where('model_type', (new (self::getAutomationMailClass()))->getMorphClass());
            })
            ->select('content_item_id')
            ->distinct()
            ->get()
            ->mapWithKeys(function (Open $open) {
                return [$open->contentItem->model->id => $open->contentItem->model->name];
            })->toArray();
    }

    public function getValue(): mixed
    {
        $value = parent::getValue();

        if (empty($value)) {
            return null;
        }

        return $value;
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => __mc('Opened Any'),
                'none' => __mc('Did Not Open Any'),
                'equals' => __mc('Opened'),
                'not-equals' => __mc('Did Not Open'),
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberOpenedAutomationMailCondition');
    }
}
