<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Enums;

enum EventType: string
{
    case Open = 'opened';
    case Click = 'click';
    case Bounce = 'hardBounce';
    case Spam = 'spam';
}
