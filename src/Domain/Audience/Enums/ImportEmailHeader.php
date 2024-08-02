<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum ImportEmailHeader: string
{
    case email = 'email';
    case Email = 'Email'; // Substack
    case Email_Address = 'Email Address'; // Mailchimp
    case email_address = 'email address'; // Mailchimp

    public static function values(): array
    {
        return [
            self::email->value,
            self::Email->value,
            self::Email_Address->value,
            self::email_address->value,
        ];
    }
}
