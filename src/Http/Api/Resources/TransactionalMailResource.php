<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem */
class TransactionalMailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'subject' => $this->contentItem->subject,
            'from' => $this->from,
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'body' => $this->contentItem->html,
            'html' => $this->contentItem->html,
            'created_at' => $this->created_at,
        ];
    }
}
