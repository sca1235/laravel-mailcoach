<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum TagType: string
{
    case Default = 'default';
    case Mailcoach = 'mailcoach';
}
