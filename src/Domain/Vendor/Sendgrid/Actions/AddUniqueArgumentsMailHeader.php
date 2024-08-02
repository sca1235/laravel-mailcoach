<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions;

use Illuminate\Mail\Events\MessageSending;

class AddUniqueArgumentsMailHeader
{
    public function handle(MessageSending $event): void
    {
        $sendHeader = $event->message->getHeaders()->get('mailcoach-send-uuid');

        if (! $sendHeader) {
            return;
        }

        $sendUuid = $sendHeader->getBodyAsString();

        if (! $sendUuid) {
            return;
        }

        $event->message->getHeaders()->addTextHeader(
            'X-SMTPAPI',
            json_encode(['unique_args' => ['send_uuid' => $sendUuid]])
        );
    }
}
