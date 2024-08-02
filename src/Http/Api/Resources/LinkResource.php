<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Content\Models\Link */
class LinkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'url' => $this->url,
            'unique_click_count' => $this->unique_click_count,
            'click_count' => $this->click_count,
            'clicks' => ClickResource::collection($this->clicks),
        ];
    }
}
