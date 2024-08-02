<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Mailcoach\Domain\Content\Models\Unsubscribe;

/** @mixin Unsubscribe */
class UnsubscribeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'campaign_uuid' => $this->contentItem->model->uuid,
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),

            'subscriber_uuid' => $this->subscriber->uuid,
            'subscriber_email' => $this->subscriber->email,
            'subscriber' => new SubscriberResource($this->whenLoaded('subscriber')),
        ];
    }
}
