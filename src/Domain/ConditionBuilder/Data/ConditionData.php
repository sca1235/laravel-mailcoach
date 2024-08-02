<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Data;

use Illuminate\Contracts\Support\Arrayable;

abstract class ConditionData implements Arrayable
{
    abstract public static function fromArray(array $data): static;
}
