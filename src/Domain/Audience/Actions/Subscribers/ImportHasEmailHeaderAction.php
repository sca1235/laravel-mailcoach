<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Enums\ImportEmailHeader;

class ImportHasEmailHeaderAction
{
    public function execute(array $headers): bool
    {
        foreach ($this->potentialEmailKeys() as $key) {
            if (in_array($key, $headers, true)) {
                return true;
            }
        }

        return false;
    }

    protected function potentialEmailKeys(): array
    {
        return ImportEmailHeader::values();
    }
}
