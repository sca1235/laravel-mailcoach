<?php

namespace Spatie\Mailcoach\Livewire\Templates;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Actions\CreateTemplateAction;

class CreateTemplateComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public ?string $name = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
        ];
    }

    public function saveTemplate()
    {
        $this->authorize('create', self::getTemplateClass());

        $template = resolve(CreateTemplateAction::class)->execute(
            $this->validate(),
        );

        notify(__mc('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $template);
    }

    public function render()
    {
        return view('mailcoach::app.templates.create');
    }
}
