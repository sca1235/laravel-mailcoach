<?php

namespace Spatie\Mailcoach\Livewire\Automations;

class AutomationActionComponent extends AutomationComponent
{
    public string $builderName = '';

    public array $action;

    public string $uuid;

    public bool $editing = false;

    public bool $editable = true;

    public bool $deletable = true;

    public int $index = 0;

    public function rules(): array
    {
        return [];
    }

    public function edit(): void
    {
        $this->editing = true;

        $this->dispatch("editAction.{$this->builderName}", $this->uuid);
    }

    public function save(): void
    {
        if (! empty($this->rules())) {
            $this->validate();
        }

        $this->dispatch("actionSaved.{$this->builderName}", $this->uuid, $this->getData());

        $this->editing = false;
    }

    public function delete(): void
    {
        $this->dispatch("actionDeleted.{$this->builderName}", $this->uuid);

        notify(__mc('Action deleted'));
    }

    public function getData(): array
    {
        return [];
    }

    public function loadData(): void
    {
        $actionModel = self::getAutomationActionClass()::findByUuid($this->action['uuid']);

        if (! $actionModel) {
            return;
        }

        $this->action['active'] = $actionModel->activeSubscribers()->count();
        $this->action['completed'] = $actionModel->completedSubscribers()->count();
        $this->action['halted'] = $actionModel->haltedSubscribers()->count();
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.automationAction');
    }
}
