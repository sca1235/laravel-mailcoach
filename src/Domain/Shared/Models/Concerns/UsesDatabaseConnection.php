<?php

namespace Spatie\Mailcoach\Domain\Shared\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Mailcoach;

/**
 * @mixin Model
 */
trait UsesDatabaseConnection
{
    public function initializeUsesDatabaseConnection(): void
    {
        if (is_null($this->getConnectionName()) && ($connection = Mailcoach::getDatabaseConnection())) {
            $this->setConnection($connection);
        }
    }
}
