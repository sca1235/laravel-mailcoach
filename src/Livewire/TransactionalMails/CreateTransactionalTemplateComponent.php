<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\CreateTemplateAction;

class CreateTransactionalTemplateComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public ?string $name = null;

    public ?string $type = 'html';

    public array $templateOptions;

    public int|string|null $template_id = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
            'type' => ['required'],
        ];
    }

    public function mount()
    {
        $this->templateOptions = static::getTemplateClass()::orderBy('name')->get()
            ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
            ->prepend('-- None --', 0)
            ->toArray();
    }

    public function saveTemplate()
    {
        $this->authorize('create', self::getTransactionalMailClass());

        $template = resolve(CreateTemplateAction::class)->execute(
            $this->validate(),
            $this->type === 'html' ? self::getTemplateClass()::find($this->template_id) : null,
        );

        notify(__mc('Email :name was created.', ['name' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $template);
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.templates.partials.create');
    }
}
