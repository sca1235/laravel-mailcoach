<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;

class CreateAutomationMailComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public array $templateOptions;

    public ?string $name = null;

    public int|string|null $template_id = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
        ];
    }

    public function mount()
    {
        $this->templateOptions = static::getTemplateClass()::orderBy('name')->get()
            ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
            ->prepend('-- None --', 0)
            ->toArray();

        $this->template_id = array_key_first($this->templateOptions);
    }

    public function saveAutomationMail()
    {
        $automationMailClass = self::getAutomationMailClass();

        $this->authorize('create', $automationMailClass);

        $data = $this->validate();

        $automationMail = resolve(UpdateAutomationMailAction::class)->execute(
            automationMail: self::getAutomationMailClass()::create(),
            attributes: $data,
            template: $this->template_id
                ? self::getTemplateClass()::find($this->template_id)
                : null,
        );

        notify(__mc('Email :name was created.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }

    public function render()
    {
        return view('mailcoach::app.automations.mails.partials.create');
    }
}
