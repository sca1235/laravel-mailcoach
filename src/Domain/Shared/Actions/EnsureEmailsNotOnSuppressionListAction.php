<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\SuppressedEmail;

class EnsureEmailsNotOnSuppressionListAction
{
    use UsesMailcoachModels;

    public function execute(string|array $email): void
    {
        $emails = Arr::wrap($email);

        /** @var Suppression|null $firstSuppressed */
        $firstSuppressed = self::getSuppressionClass()::query()
            ->whereIn('email', $emails)
            ->first();

        if ($firstSuppressed) {
            throw SuppressedEmail::make($firstSuppressed->email);
        }
    }
}
