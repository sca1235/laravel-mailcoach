<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;

class SendWebhookActionComponent extends AutomationActionComponent
{
    use UsesMailcoachModels;

    public string $url = '';

    public string $secret = '';

    public function getData(): array
    {
        return [
            'url' => $this->url,
            'secret' => $this->secret,
        ];
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
            ],
            'secret' => ['required', 'min:8'],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.sendWebhookAction');
    }
}
