<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Enums;

// reference: https://postmarkapp.com/developer/api/bounce-api#bounce-types
enum BounceType: string
{
    case DnsError = 'DnsError';
    case Transient = 'Transient';
    case SpamNotification = 'SpamNotification';
    case SoftBounce = 'SoftBounce';
    case Undeliverable = 'Undeliverable';
    case HardBounce = 'HardBounce';

    public static function softBounces(): array
    {
        return [
            self::DnsError->value,
            self::Transient->value,
            self::SpamNotification->value,
            self::SoftBounce->value,
            self::Undeliverable->value,
        ];
    }
}
