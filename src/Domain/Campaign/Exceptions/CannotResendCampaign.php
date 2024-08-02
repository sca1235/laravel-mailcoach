<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use RuntimeException;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CannotResendCampaign extends RuntimeException
{
    public static function notCancelled(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` can't be resent, because it is not cancelled.");
    }
}
