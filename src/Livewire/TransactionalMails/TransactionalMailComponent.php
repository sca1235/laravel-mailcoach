<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class TransactionalMailComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TransactionalMailLogItem $transactionalMail;

    public function mount(TransactionalMailLogItem $transactionalMail)
    {
        $this->transactionalMail = $transactionalMail;
    }

    public function resend()
    {
        $this->transactionalMail->resend();

        notify(__mc('The mail has been resent!'));
    }

    public function downloadAttachment(int $id)
    {
        $attachment = $this->transactionalMail->getMedia('attachments')->find($id);

        abort_unless($attachment, 404);

        return $attachment;
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.show')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __mc('Email to :email', ['email' => $this->transactionalMail->toString()]),
                'originTitle' => __mc('Transactional'),
                'originHref' => route('mailcoach.transactional'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
