<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalTemplateSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    public bool $dirty = false;

    public ?string $name = null;

    public ?string $type = null;

    public bool $store_mail = false;

    protected function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'store_mail' => '',
        ];
    }

    public function mount(TransactionalMail $transactionalMailTemplate)
    {
        $this->authorize('update', $transactionalMailTemplate);

        $this->template = $transactionalMailTemplate;
        $this->fill($this->template->toArray());
    }

    public function updated()
    {
        $this->dirty = true;
    }

    public function save()
    {
        $this->validate();

        $this->template->name = $this->name;
        $this->template->type = $this->type;
        $this->template->store_mail = $this->store_mail;
        $this->template->save();

        notify(__mc('Template :template was updated.', ['template' => $this->template->name]));

        $this->dirty = false;
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.settings')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => $this->name,
                'originTitle' => __mc('Transactional'),
                'originHref' => route('mailcoach.transactional'),
                'template' => $this->template,
            ]);
    }
}
