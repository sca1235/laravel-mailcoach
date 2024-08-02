<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Spatie\Mailcoach\Domain\Automation\Enums\WaitUnit;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\AttributeCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;

class ConditionActionComponent extends AutomationActionComponent
{
    public string $length = '1';

    public string $unit;

    public array $units = [];

    public array $editingActions = [];

    public array $yesActions = [];

    public array $noActions = [];

    public string $condition = '';

    public array $conditionOptions = [];

    public array $conditionData = [];

    public function getData(): array
    {
        return [
            'length' => (int) $this->length,
            'unit' => $this->unit,
            'condition' => $this->condition,
            'conditionData' => $this->conditionData,
            'yesActions' => $this->yesActions,
            'noActions' => $this->noActions,
        ];
    }

    public function updatedCondition()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $condition = $this->condition;

        if (! method_exists($condition, 'rules')) {
            return;
        }

        /** @var HasTagCondition|HasOpenedAutomationMail|HasClickedAutomationMail|AttributeCondition $condition */
        foreach (array_keys($condition::rules($this->conditionData)) as $key) {
            if (! isset($this->conditionData[$key])) {
                $this->conditionData[$key] = '';
            }
        }
    }

    public function mount()
    {
        $this->units = WaitUnit::options();
        $this->unit ??= WaitUnit::Days->value;

        $defaultConditions = collect([
            AttributeCondition::class,
            HasTagCondition::class,
            HasOpenedAutomationMail::class,
            HasClickedAutomationMail::class,
        ]);

        $customConditions = collect(config('mailcoach.automation.flows.conditions', []))
            ->filter(fn ($condition) => in_array(Condition::class, class_implements($condition)));

        $this->conditionOptions = $defaultConditions
            ->merge($customConditions)
            ->mapWithKeys(function ($class) {
                return [$class => $class::getName()];
            })->toArray();

        $this->unit = Str::plural($this->unit);

        if ($this->condition === HasTagCondition::class && is_array($this->conditionData['tag'])) {
            $this->conditionData['tag'] = $this->conditionData['tag']['value'];
        }
    }

    #[On('automationBuilderUpdated.{uuid}-yes-actions')]
    public function yesActionsUpdated(array $data)
    {
        $this->yesActions = $data['actions'];

        $this->dispatch("actionSaved.{$this->builderName}", $this->uuid, $this->getData());
    }

    #[On('automationBuilderUpdated.{uuid}-no-actions')]
    public function noActionsUpdated(array $data): void
    {
        $this->noActions = $data['actions'];

        $this->dispatch("actionSaved.{$this->builderName}", $this->uuid, $this->getData());
    }

    #[On('editAction.{uuid}-yes-actions')]
    #[On('editAction.{uuid}-no-actions')]
    public function editAction(string $uuid)
    {
        $this->editingActions[] = $uuid;
    }

    #[On('actionSaved.{uuid}-yes-actions')]
    #[On('actionSaved.{uuid}-no-actions')]
    public function actionSaved(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    #[On('actionDeleted.{uuid}-yes-actions')]
    #[On('actionDeleted.{uuid}-no-actions')]
    public function actionDeleted(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function rules(): array
    {
        $rules = [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(array_keys($this->units))],
            'condition' => ['required'],
            'conditionData' => ['required', 'array'],
            'yesActions' => ['nullable', 'array'],
            'noActions' => ['nullable', 'array'],
        ];

        if (! method_exists($this->condition, 'rules')) {
            return $rules;
        }

        $conditionRules = collect($this->condition ? $this->condition::rules($this->conditionData) : [])->mapWithKeys(function ($rules, $key) {
            return ["conditionData.{$key}" => $rules];
        })->toArray();

        return [...$rules, ...$conditionRules];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.conditionAction');
    }
}
