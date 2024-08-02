<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend\Enums;

enum EventType: string
{
    case Bounced = 'bounced';
    case Clicked = 'clicked';
    case Complained = 'complained';
    case Opened = 'opened';
}
