<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Mail\TestMail;
use Symfony\Component\Mime\Email;

class SendTestComponent extends Component
{
    public string $mailer = '';

    public string $from_email = '';

    public string $to_email = '';

    public function mount(string $mailer)
    {
        $this->mailer = $mailer;
        $this->from_email = auth()->guard(config('mailcoach.guard'))->user()->email;
        $this->to_email = auth()->guard(config('mailcoach.guard'))->user()->email;
    }

    public function sendTest()
    {
        $this->validate([
            'from_email' => ['required', 'email:strict'],
            'to_email' => ['required', 'email:strict'],
        ]);

        try {
            $mail = new TestMail($this->from_email, $this->to_email);
            $mail->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
            });

            Mail::mailer($this->mailer)->send($mail);
        } catch (\Throwable $e) {
            notifyError($e->getMessage());
            $this->dispatch('modal-closed', ['modal' => 'send-test']);

            return;
        }

        notify(__mc('A test mail has been sent to :email. Please check if it arrived.', ['email' => $this->to_email]));

        $this->dispatch('modal-closed', ['modal' => 'send-test']);
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.partials.sendTest');
    }
}
