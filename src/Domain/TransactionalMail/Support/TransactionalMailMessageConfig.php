<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support;

use Symfony\Component\Mime\Email;

class TransactionalMailMessageConfig
{
    public const HEADER_NAME_TRANSACTIONAL = 'x-mailcoach-transactional-mail';

    public const HEADER_NAME_OPENS = 'x-mailcoach-transactional-mail-config-track-opens';

    public const HEADER_NAME_CLICKS = 'x-mailcoach-transactional-mail-config-track-clicks';

    public const HEADER_NAME_STORE = 'x-mailcoach-transactional-mail-config-store';

    public const HEADER_NAME_MAIL_NAME = 'x-mailcoach-transactional-mail-config-mail-name';

    public const HEADER_NAME_MAILABLE_CLASS = 'x-mailcoach-transactional-mail-config-mailable-class';

    public static function createForMessage(Email $message): self
    {
        return new self($message);
    }

    protected function __construct(
        protected Email $message
    ) {
    }

    public function isTransactionalMail(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_TRANSACTIONAL);
    }

    public function shouldStore(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_STORE);
    }

    public function getMailableClass(): string
    {
        return $this->message->getHeaders()->get(static::HEADER_NAME_MAILABLE_CLASS)->getBodyAsString();
    }

    public function getMailName(): ?string
    {
        return $this->message->getHeaders()->get(static::HEADER_NAME_MAIL_NAME)?->getBodyAsString();
    }

    public static function getHeaderNamesToReset(): array
    {
        return [
            static::HEADER_NAME_OPENS,
            static::HEADER_NAME_CLICKS,
            static::HEADER_NAME_STORE,
            static::HEADER_NAME_MAILABLE_CLASS,
        ];
    }
}
