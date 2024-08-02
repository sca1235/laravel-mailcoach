<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Enums;

enum PostMarkTrigger: string
{
    case Open = 'Open';
    case Click = 'Click';
    case Bounce = 'Bounce';
    case SpamComplaint = 'SpamComplaint';
}
