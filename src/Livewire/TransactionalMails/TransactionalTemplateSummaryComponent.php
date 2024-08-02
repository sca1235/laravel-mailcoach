<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalTemplateSummaryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    public function mount(TransactionalMail $transactionalMailTemplate)
    {
        $this->template = $transactionalMailTemplate;
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.summary')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => $this->template->name,
                'originTitle' => __mc('Transactional'),
                'originHref' => route('mailcoach.transactional'),
                'template' => $this->template,
            ]);
    }
}
