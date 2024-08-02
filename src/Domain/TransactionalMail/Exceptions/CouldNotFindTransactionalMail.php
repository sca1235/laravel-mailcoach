<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Exceptions;

use Exception;
use Illuminate\Mail\Mailable;

class CouldNotFindTransactionalMail extends Exception
{
    public static function make(string $name, Mailable $mailable): self
    {
        $mailableClass = $mailable::class;

        return new static("Could not send mailable `$mailableClass` because no transactional mail named `$name` could be found.");
    }
}
