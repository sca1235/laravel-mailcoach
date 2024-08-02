<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;
use Spatie\ValidationRules\Rules\Delimited;

class RemoveTagsActionComponent extends AutomationActionComponent
{
    public string $tags = '';

    public function getData(): array
    {
        return [
            'tags' => $this->tags,
        ];
    }

    public function rules(): array
    {
        return [
            'tags' => ['required', new Delimited('string')],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.removeTagsAction');
    }
}
