<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;
use Spatie\Mailcoach\Mailcoach;

class SendTransactionalMailAction
{
    public function execute(SendTransactionalMailRequest $request): void
    {
        $transactionalMail = $this->createTransactionalMail($request);

        if ($request->fake) {
            $this->fakeMail($transactionalMail);

            return;
        }

        $this->ensureEmailsNotOnSuppressionList($transactionalMail);

        $name = $request->get('mailer', Mailcoach::defaultTransactionalMailer());

        Mail::mailer($name)->send($transactionalMail, ['fake' => $request->fake]);
    }

    protected function createTransactionalMail(SendTransactionalMailRequest $request): TransactionalMail
    {
        $normalizer = new AddressNormalizer();

        return new TransactionalMail(
            mailName: $request->get('mail_name'),
            subject: $request->get('subject') ?? '',
            from: $normalizer->normalize($request->get('from')),
            to: $normalizer->normalize($request->get('to')),
            cc: $normalizer->normalize($request->get('cc')),
            bcc: $normalizer->normalize($request->get('bcc')),
            replyTo: $normalizer->normalize($request->get('reply_to')),
            mailer: $request->get('mailer'),
            replacements: $request->replacements(),
            attachments: $request->attachments(),
            store: $request->shouldStoreMail(),
            html: $request->html,
        );
    }

    protected function fakeMail(TransactionalMail $transactionalMail): void
    {
        $transactionalMail->build();
        $emailMock = $transactionalMail->toEmail();
        $emailMock->getHeaders()
            ->addTextHeader(TransactionalMailMessageConfig::HEADER_NAME_STORE, true)
            ->addTextHeader(TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, TransactionalMail::class);

        (new StoreTransactionalMail())
            ->handle(new MessageSending(($emailMock), ['fake' => true]));
    }

    protected function getAllAddresses(TransactionalMail $transactionalMail): Collection
    {
        return collect($transactionalMail->to)
            ->merge($transactionalMail->cc)
            ->merge($transactionalMail->bcc)
            ->unique()
            ->pluck('address');
    }

    protected function ensureEmailsNotOnSuppressionList(TransactionalMail $transactionalMail): void
    {
        $action = Mailcoach::getSharedActionClass('is_on_suppression_list', EnsureEmailsNotOnSuppressionListAction::class);

        $action->execute(
            $this->getAllAddresses($transactionalMail)->toArray()
        );
    }
}
