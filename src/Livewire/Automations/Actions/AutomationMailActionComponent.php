<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;

class AutomationMailActionComponent extends AutomationActionComponent
{
    use UsesMailcoachModels;

    public int|string $automation_mail_id = '';

    public array $mailOptions;

    public function mount()
    {
        $this->mailOptions = self::getAutomationMailClass()::query()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getData(): array
    {
        return [
            'automation_mail_id' => $this->automation_mail_id,
        ];
    }

    public function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailClass(), 'id'),
            ],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.automationMailAction');
    }
}
