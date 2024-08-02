<?php

namespace Spatie\Mailcoach\Domain\Campaign\Enums;

use Filament\Support\Contracts\HasLabel;

enum CampaignStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Sending = 'sending';
    case Paused = 'paused';
    case Sent = 'sent';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => __mc('Draft'),
            self::Sending => __mc('Sending'),
            self::Paused => __mc('Paused'),
            self::Sent => __mc('Sent'),
            self::Cancelled => __mc('Cancelled'),
        };
    }
}
