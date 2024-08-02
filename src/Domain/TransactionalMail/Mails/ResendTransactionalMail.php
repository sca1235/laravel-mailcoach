<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ResendTransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;

    public function __construct(
        public TransactionalMailLogItem $originalMail
    ) {
        $this
            ->from($this->originalMail->from)
            ->to($this->originalMail->to)
            ->cc($this->originalMail->cc)
            ->bcc($this->originalMail->bcc)
            ->subject($this->originalMail->contentItem->subject);
    }

    public function build(): void
    {
        $this->view('mailcoach::mails.transactionalMails.resend');

        $this->setMailableClassHeader($this->originalMail->mailable_class);
    }

    public function attachments(): array
    {
        return $this->originalMail->getMedia('attachments')
            ->map(fn (Media $media) => $media->toMailAttachment())
            ->toArray();
    }
}
