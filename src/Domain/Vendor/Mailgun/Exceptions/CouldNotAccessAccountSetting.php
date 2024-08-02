<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun\Exceptions;

use Exception;

class CouldNotAccessAccountSetting extends Exception
{
    public static function make(string $settingName): self
    {
        return new self("Could not access account setting `{$settingName}`. Make sure the provided API key has the right permissions.");
    }
}
