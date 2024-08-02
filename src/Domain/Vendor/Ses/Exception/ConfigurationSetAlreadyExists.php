<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Exception;

class ConfigurationSetAlreadyExists extends \Exception
{
    public static function make(string $name): static
    {
        return new static("There already exist a configuration set named `{$name}`.");
    }
}
