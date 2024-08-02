<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Http\Api\Queries\TransactionalMailQuery;
use Spatie\Mailcoach\Http\Api\Resources\TransactionalMailResource;

class TransactionalMailsController
{
    public function __invoke(TransactionalMailQuery $transactionalMailsQuery)
    {
        return TransactionalMailResource::collection($transactionalMailsQuery->paginate());
    }
}
