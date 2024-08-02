<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Livewire\Attributes\On;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;

class SplitActionComponent extends AutomationActionComponent
{
    public array $editingActions = [];

    public array $leftActions = [];

    public array $rightActions = [];

    public function getData(): array
    {
        return [
            'leftActions' => $this->leftActions,
            'rightActions' => $this->rightActions,
        ];
    }

    #[On('automationBuilderUpdated.{uuid}-left-actions')]
    public function leftActionsUpdated(array $data)
    {
        $this->leftActions = $data['actions'];

        $this->dispatch("actionSaved.{$this->builderName}", $this->uuid, $this->getData());
    }

    #[On('automationBuilderUpdated.{uuid}-right-actions')]
    public function rightActionsUpdated(array $data): void
    {
        $this->rightActions = $data['actions'];

        $this->dispatch("actionSaved.{$this->builderName}", $this->uuid, $this->getData());
    }

    #[On('editAction.{uuid}-left-actions')]
    #[On('editAction.{uuid}-right-actions')]
    public function editAction(string $uuid)
    {
        $this->editingActions[] = $uuid;
    }

    #[On('actionSaved.{uuid}-left-actions')]
    #[On('actionSaved.{uuid}-right-actions')]
    public function actionSaved(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    #[On('actionDeleted.{uuid}-left-actions')]
    #[On('actionDeleted.{uuid}-right-actions')]
    public function actionDeleted(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function rules(): array
    {
        return [
            'leftActions' => ['nullable', 'array'],
            'rightActions' => ['nullable', 'array'],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.splitAction');
    }
}
