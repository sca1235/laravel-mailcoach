<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

/** @mixin Send */
class SendResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'transport_message_id' => $this->transport_message_id,
            'campaign_uuid' => $this->contentItem->model instanceof Campaign
                ? $this->contentItem->model->uuid
                : null,
            'automation_mail_uuid' => $this->contentItem->model instanceof AutomationMail
                ? $this->contentItem->model->uuid
                : null,
            'transactional_mail_log_item_uuid' => $this->contentItem->model instanceof TransactionalMailLogItem
                ? $this->contentItem->model->uuid
                : null,
            'subscriber_uuid' => $this->subscriber?->uuid,
            'sent_at' => $this->sent_at,
            'failed_at' => $this->failed_at,
            'failure_reason' => $this->failure_reason,
            'open_count' => $this->opens->count(),
            'click_count' => $this->clicks->count(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
