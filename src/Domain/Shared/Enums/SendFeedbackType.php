<?php

namespace Spatie\Mailcoach\Domain\Shared\Enums;

enum SendFeedbackType: string
{
    case Bounce = 'bounce';
    case SoftBounce = 'soft_bounce';
    case Complaint = 'complaint';
    case Suppressed = 'suppressed';
}
