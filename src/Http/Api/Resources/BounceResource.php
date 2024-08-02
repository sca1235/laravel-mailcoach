<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem */
class BounceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'subscriber_uuid' => $this->subscriber_uuid,
            'subscriber_email_list_uuid' => $this->subscriber_email_list_uuid,
            'subscriber_email' => $this->subscriber_email,
            'bounce_count' => (int) $this->bounce_count,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
