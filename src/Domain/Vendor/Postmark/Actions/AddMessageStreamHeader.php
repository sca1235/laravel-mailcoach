<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Actions;

use Illuminate\Mail\Events\MessageSending;

class AddMessageStreamHeader
{
    public function handle(MessageSending $event): void
    {
        $driver = config('mailcoach.mailer') ?? config('mailcoach.campaigns.mailer') ?? config('mail.default');

        if (config("mail.mailers.{$driver}.transport") !== 'postmark') {
            return;
        }

        if (! $messageStream = config('mailcoach.postmark_feedback.message_stream')) {
            return;
        }

        if (! $event->message->getHeaders()->get('X-MAILCOACH')) {
            return;
        }

        $event->message->getHeaders()->remove('X-PM-Message-Stream');
        $event->message->getHeaders()->addTextHeader('X-PM-Message-Stream', $messageStream);
    }
}
