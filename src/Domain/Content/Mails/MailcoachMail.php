<?php

namespace Spatie\Mailcoach\Domain\Content\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Symfony\Component\Mime\Email;

class MailcoachMail extends Mailable
{
    use SerializesModels;

    public ?ContentItem $contentItem = null;

    public ?Send $send = null;

    public string $htmlContent = '';

    public string $textContent = '';

    public ?string $fromEmail = null;

    public ?string $fromName = null;

    /** @deprecated will be removed in v7 */
    public ?string $replyToEmail = null;

    /** @deprecated will be removed in v7 */
    public ?string $replyToName = null;

    /** @var array<array{email: string, name: ?string}> */
    public array $replyToAll = [];

    public $htmlView = null;

    public $textView = null;

    public function setSend(Send $send): static
    {
        $this->send = $send;

        $this->contentItem = $send->contentItem;

        return $this;
    }

    public function setFrom(string $fromEmail, ?string $fromName = null): static
    {
        $this->fromEmail = $fromEmail;

        $this->fromName = $fromName;

        return $this;
    }

    public function setReplyTo(string $replyToEmail, ?string $replyToName = null): static
    {
        $this->replyToEmail = $replyToEmail;

        $this->replyToName = $replyToName;

        return $this;
    }

    public function setHtmlView(string $htmlView): static
    {
        $this->htmlView = $htmlView;

        return $this;
    }

    public function setTextView(string $textView): static
    {
        $this->textView = $textView;

        return $this;
    }

    public function setContentItem(ContentItem $contentItem): static
    {
        $this->contentItem = $contentItem;

        $this->setFrom(
            $contentItem->getFromEmail($this->send),
            $contentItem->getFromName($this->send),
        );

        $this->replyToAll = $contentItem->getReplyToAddresses($this->send);

        $replyTo = $contentItem->getReplyToEmail($this->send);

        if ($replyTo) {
            $replyToName = $contentItem->getReplyToName($this->send);
            $this->setReplyTo($replyTo, $replyToName);
        }

        $this
            ->setHtmlView('mailcoach::mails.html')
            ->setTextView('mailcoach::mails.text');

        return $this;
    }

    public function setHtmlContent(string $htmlContent = ''): static
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    public function setTextContent(string $textContent): static
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function subject($subject): static
    {
        if (! empty($this->subject)) {
            return $this;
        }

        $this->subject = $subject;

        return $this;
    }

    public function build()
    {
        $mail = $this
            ->from($this->fromEmail, $this->fromName)
            ->subject($this->subject)
            ->view($this->htmlView)
            ->text($this->textView)
            ->addUnsubscribeHeaders()
            ->storeTransportMessageId();

        if ($this->replyToAll !== []) {
            foreach ($this->replyToAll as $replyTo) {
                $mail->replyTo($replyTo['email'], $replyTo['name']);
            }
        } elseif ($this->replyToEmail) {
            $mail->replyTo($this->replyToEmail, $this->replyToName);
        }

        return $mail;
    }

    protected function addUnsubscribeHeaders(): static
    {
        if (is_null($this->send)) {
            return $this;
        }

        $this->withSymfonyMessage(function (Email $message) {
            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe',
                    '<'.$this->send->subscriber->unsubscribeUrl($this->send).'>'
                );

            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe-Post',
                    'List-Unsubscribe=One-Click'
                );

            if ($this->send->subscriber->emailList) {
                $message
                    ->getHeaders()
                    ->addTextHeader(
                        'List-ID',
                        '<'.$this->send->subscriber->emailList->uuid.strstr($this->fromEmail, '@').'>'
                    );
            }

            $message
                ->getHeaders()
                ->addTextHeader(
                    'mailcoach-send-uuid',
                    $this->send->uuid
                );
        });

        return $this;
    }

    protected function storeTransportMessageId(): static
    {
        if (is_null($this->send)) {
            return $this;
        }

        $this->withSymfonyMessage(function (Email $message) {
            $messageId = $message->generateMessageId();
            $message->getHeaders()->addIdHeader('Message-ID', $messageId);
            $this->send->storeTransportMessageId($messageId);
        });

        return $this;
    }
}
