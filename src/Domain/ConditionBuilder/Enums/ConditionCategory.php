<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Enums;

enum ConditionCategory: string
{
    case Tags = 'tags';
    case Actions = 'actions';
    case Attributes = 'attributes';

    public function label(): string
    {
        return match ($this) {
            self::Tags => __mc('Tags'),
            self::Actions => __mc('Actions'),
            self::Attributes => __mc('Attributes'),
        };
    }
}
