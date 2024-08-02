<?php

namespace Spatie\Mailcoach\Domain\Audience\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;

class ExportSubscribersResultMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public function __construct(public SubscriberExport $subscriberExport)
    {
    }

    public function build()
    {
        return $this
            ->from(
                $this->subscriberExport->emailList->default_from_email,
                $this->subscriberExport->emailList->default_from_name
            )
            ->subject(__mc('Export Subscribers Result Mail'))
            ->markdown('mailcoach::mails.exportResults');
    }
}
