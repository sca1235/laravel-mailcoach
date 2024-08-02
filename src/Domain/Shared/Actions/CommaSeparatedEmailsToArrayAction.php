<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

class CommaSeparatedEmailsToArrayAction
{
    /** @return array<array{email: string, name: ?string}> */
    public function execute(?string $emails, ?string $names): array
    {
        if (! $emails) {
            return [];
        }

        $emails = explode(',', $emails);
        $names = ! empty($names) ? explode(',', $names) : null;

        if (! $emails) {
            return [];
        }

        $users = [];
        foreach ($emails as $index => $email) {
            $email = trim($email);

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $users[] = [
                'email' => $email,
                'name' => isset($names[$index]) ? trim($names[$index]) : null,
            ];
        }

        return $users;
    }
}
