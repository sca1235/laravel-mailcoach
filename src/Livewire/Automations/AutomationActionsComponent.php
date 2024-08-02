<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationActionsComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public array $editingActions = [];

    public array $actions = [];

    public bool $unsavedChanges = false;

    public string $selectedTrigger;

    public Collection $triggerOptions;

    public function mount()
    {
        $this->triggerOptions = collect(config('mailcoach.automation.flows.triggers'))
            ->mapWithKeys(function (string $trigger) {
                return [$trigger => $trigger::getName()];
            });

        $this->selectedTrigger = $this->automation->triggerClass();

        $this->actions = $this->automation->actions()
            ->get()
            ->map(function (Action $action) {
                try {
                    return $action->toLivewireArray();
                } catch (ModelNotFoundException) {
                    $action->delete();

                    return null;
                }
            })
            ->filter()
            ->values()
            ->toArray();

        app(MainNavigation::class)->activeSection()?->add($this->automation->name, route('mailcoach.automations'));
    }

    #[On('editAction.default')]
    public function editAction(string $uuid): void
    {
        $this->editingActions[] = $uuid;
        $this->unsavedChanges = true;
    }

    #[On('actionSaved.default')]
    public function actionSaved(string $uuid): void
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    #[On('actionDeleted.default')]
    public function actionDeleted(string $uuid): void
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
        $this->unsavedChanges = true;
    }

    #[On('automationBuilderUpdated.default')]
    public function automationBuilderUpdated($data): void
    {
        $this->actions = $data['actions'];
        $this->unsavedChanges = true;
    }

    public function rules(): array
    {
        return [
            'selectedTrigger' => ['required', Rule::in(config('mailcoach.automation.flows.triggers'))],
        ];
    }

    public function save(string $formData): void
    {
        parse_str($formData, $data);

        $this->validate();

        $validator = Validator::make($data, $this->selectedTrigger::rules());
        $triggerData = $validator->validate();

        $this->automation->triggerOn($this->selectedTrigger::make($triggerData));
        $this->automation->chain($this->actions);

        notify(__mc('Actions successfully saved to automation :automation.', [
            'automation' => $this->automation->name,
        ]));

        $this->unsavedChanges = false;
    }

    public function render()
    {
        return view('mailcoach::app.automations.actions')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => $this->automation->name,
                'originTitle' => __mc('Automations'),
                'originHref' => route('mailcoach.automations'),
                'hideFooter' => true,
                'fullWidth' => true,
            ]);
    }
}
