<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Content\Models\Click */
class ClickResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'send_uuid' => $this->send->uuid,
            'link_uuid' => $this->link->uuid,
            'subscriber_uuid' => $this->subscriber->uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
