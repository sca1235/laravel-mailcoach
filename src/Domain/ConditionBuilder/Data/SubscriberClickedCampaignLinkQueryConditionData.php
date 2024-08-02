<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Data;

class SubscriberClickedCampaignLinkQueryConditionData extends ConditionData
{
    protected function __construct(
        public ?int $campaignId,
        public ?string $link = null,
    ) {
    }

    public static function make(?int $campaignId, ?string $link = null): static
    {
        return new static($campaignId, $link);
    }

    public static function fromArray(array $data): static
    {
        return new static($data['campaignId'], $data['link'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'campaignId' => $this->campaignId,
            'link' => $this->link,
        ];
    }
}
