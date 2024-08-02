<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Exceptions;

use RuntimeException;

class SuppressedEmail extends RuntimeException
{
    public static function make(string $email): self
    {
        return new self("The email `{$email}` is on the suppression list.");
    }
}
