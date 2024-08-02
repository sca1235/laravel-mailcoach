<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Enums;

enum InputType: string
{
    case Text = 'text';
    case Date = 'date';
    case Email = 'email';
    case Radio = 'radio';
}
