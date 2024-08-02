<?php

namespace Spatie\Mailcoach\Domain\Settings\Enums;

enum MailerTransport: string
{
    case Brevo = 'brevo';
    case Mailgun = 'mailgun';
    case Postmark = 'postmark';
    case Resend = 'resend';
    case SendGrid = 'sendGrid';
    case Ses = 'ses';
    case Smtp = 'smtp';

    public function label(): string
    {
        return match ($this) {
            self::Brevo => 'Brevo',
            self::Mailgun => 'Mailgun',
            self::Postmark => 'Postmark',
            self::Resend => 'Resend',
            self::SendGrid => 'SendGrid',
            self::Ses => 'Amazon SES',
            self::Smtp => 'SMTP',
        };
    }
}
