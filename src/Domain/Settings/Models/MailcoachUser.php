<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

/** @mixin \Illuminate\Database\Eloquent\Model */
interface MailcoachUser
{
    public function canViewMailcoach(): bool;
}
