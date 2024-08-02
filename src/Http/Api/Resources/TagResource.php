<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Audience\Models\Tag */
class TagResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email_list' => $this->whenLoaded('emailList', function () {
                return EmailListResource::make($this->emailList);
            }),
            'email_list_uuid' => $this->emailList->uuid,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
