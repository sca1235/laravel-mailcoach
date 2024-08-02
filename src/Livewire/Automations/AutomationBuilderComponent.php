<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class AutomationBuilderComponent extends Component
{
    public string $name = '';

    public Automation $automation;

    public array $actions = [];

    public string $selectedTrigger;

    public function mount()
    {
        $this->selectedTrigger = $this->automation->triggerClass();
    }

    #[On('actionSaved.{name}')]
    public function actionSaved(string $uuid, array $actionData): void
    {
        $index = collect($this->actions)->search(function ($action) use ($uuid) {
            return $action['uuid'] === $uuid;
        });

        if ($index === false) {
            return;
        }

        $this->actions[$index]['data'] = $actionData;

        $this->dispatch("automationBuilderUpdated.{$this->name}", $this->getData());
    }

    #[On('actionDeleted.{name}')]
    public function actionDeleted(string $uuid): void
    {
        $index = collect($this->actions)->search(function ($action) use ($uuid) {
            return $action['uuid'] === $uuid;
        });

        if ($index === false) {
            return;
        }

        unset($this->actions[$index]);

        $this->actions = array_values($this->actions);

        $this->dispatch("automationBuilderUpdated.{$this->name}", $this->getData());
    }

    public function addAction(string $actionClass, int $index): void
    {
        $uuid = Str::uuid()->toString();
        $editable = (bool) $actionClass::getComponent();

        array_splice($this->actions, $index, 0, [[
            'uuid' => $uuid,
            'class' => $actionClass,
            'data' => [
                'editing' => $editable,
                'editable' => $editable,
            ],
            'active' => 0,
            'completed' => 0,
            'halted' => 0,
        ]]);

        if ($editable) {
            $this->dispatch("editAction.{$this->name}", $uuid);
        }

        $this->dispatch("automationBuilderUpdated.{$this->name}", $this->getData());
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'actions' => $this->actions,
        ];
    }

    public function render()
    {
        $actionOptions = collect(config('mailcoach.automation.flows.actions'))
            ->groupBy(fn (string $action) => $action::getCategory()->value);

        $triggerOptions = collect(config('mailcoach.automation.flows.triggers'))
            ->mapWithKeys(function (string $trigger) {
                return [$trigger => $trigger::getName()];
            });

        return view('mailcoach::app.automations.components.automationBuilder', [
            'actionOptions' => $actionOptions,
            'triggerOptions' => $triggerOptions,
            'actions' => $this->actions,
        ]);
    }

    public function updated($fieldName): void
    {
        $this->resetValidation($fieldName);

        $this->dispatch("automationBuilderUpdated.{$this->name}", $this->getData());
    }
}
