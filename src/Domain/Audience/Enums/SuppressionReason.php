<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

use Filament\Support\Contracts\HasLabel;

enum SuppressionReason: string implements HasLabel
{
    case hardBounce = 'hard_bounce';
    case spamComplaint = 'spam_complaint';
    case manual = 'manual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::hardBounce => __mc('Hard bounce'),
            self::spamComplaint => __mc('Spam complaint'),
            self::manual => __mc('Manual'),
        };
    }
}
