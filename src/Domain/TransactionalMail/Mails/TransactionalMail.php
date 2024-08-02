<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @property array|null $replacers
 */
class TransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;
    use UsesMailcoachTemplate;

    private array $embeddedAttachments;

    private array $attachedAttachments;

    private array $fields;

    public function __construct(
        private ?string $mailName,
        string $subject,
        array|string $from,
        /** @param  array<int, Address>  $to */
        array $to,
        array $cc = [],
        array $bcc = [],
        array $replyTo = [],
        ?string $mailer = null,
        private array $replacements = [],
        array $attachments = [],
        bool $store = true,
        protected $html = null,
    ) {
        $this
            ->setTransactionalHeader()
            ->prepareAttachment($attachments)
            ->prepareHtml($html);

        $this
            ->when($store, fn (TransactionalMail $mail) => $mail->store())
            ->from($from)
            ->to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->replyTo($replyTo)
            ->subject($subject)
            ->mailer($mailer ?? Mailcoach::defaultTransactionalMailer())
            ->view('mailcoach::mails.transactionalMails.mail');
    }

    public function build(): void
    {
        if ($this->shouldUseMailcoachTemplate()) {
            $this->html = null;

            $this->template(
                $this->mailName,
                $this->replacements,
            );
        } else {
            $this->view('mailcoach::mails.transactionalMails.template', ['content' => $this->html]);
        }

        $this->withSymfonyMessage(function (Email $email) {
            foreach ($this->embeddedAttachments as $embeddedAttachment) {
                $email->embed(
                    body: base64_decode($embeddedAttachment['content']),
                    name: $embeddedAttachment['name'],
                    contentType: $embeddedAttachment['content_type'],
                );
            }

            foreach ($this->attachedAttachments as $attachedAttachment) {
                $email->attach(
                    body: base64_decode($attachedAttachment['content']),
                    name: $attachedAttachment['name'],
                    contentType: $attachedAttachment['content_type'],
                );
            }
        });
    }

    public function toEmail(): Email
    {
        $normalizer = new AddressNormalizer();

        $from = is_array($this->from) ? array_map(fn ($user) => $user['address'], $this->from) : $this->from;
        $to = is_array($this->to) ? array_map(fn ($user) => $user['address'], $this->to) : $this->to;
        $cc = is_array($this->cc) ? array_map(fn ($user) => $user['address'], $this->cc) : $this->cc;
        $bcc = is_array($this->bcc) ? array_map(fn ($user) => $user['address'], $this->bcc) : $this->bcc;

        return (new Email())
            ->subject($this->subject)
            ->from(...$normalizer->normalize(...$from))
            ->to(...$normalizer->normalize(...$to))
            ->cc(...$normalizer->normalize(...$cc))
            ->bcc(...$normalizer->normalize(...$bcc))
            ->html($this->html);
    }

    protected function prepareAttachment(array $attachments): self
    {
        $this->embeddedAttachments = array_filter(
            $attachments,
            fn ($attachment) => ! is_null($attachment['content_id'] ?? null),
        );

        $this->attachedAttachments = array_filter(
            $attachments,
            fn ($attachment) => is_null($attachment['content_id'] ?? null),
        );

        return $this;
    }

    protected function prepareHtml(?string $html): self
    {
        if ($this->shouldUseMailcoachTemplate()) {
            return $this;
        }

        $this->html = $html;

        if (! str_contains($html, '<html')) {
            $this->html = "<html><body>{$this->html}</body></html>";
        }

        return $this;
    }

    protected function shouldUseMailcoachTemplate(): bool
    {
        if ($this->html === 'use-mailcoach-mail') {
            return true;
        }

        if (! $this->html) {
            return true;
        }

        return ! empty($this->mailName);
    }
}
