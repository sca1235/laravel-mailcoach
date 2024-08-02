<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\ConditionBuilder\Data\SubscriberClickedCampaignLinkQueryConditionData;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberClickedCampaignLinkConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public EmailList $emailList;

    public ?int $campaignId = null;

    public ?string $link = null;

    public array $campaigns = [];

    public array $options = [];

    public array $comparisonOptions;

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->storedCondition['value']['campaignId'] ??= null;
        $this->storedCondition['value']['link'] ??= null;

        $this->campaignId = $this->campaignId();
        $this->link = $this->link();
        $this->campaigns = self::getCampaignClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->has('contentItem.links')
            ->pluck('id', 'name')
            ->mapWithKeys(function (string $id, string $name) {
                return [$id => $name];
            })->toArray();
    }

    public function getValue(): mixed
    {
        return SubscriberClickedCampaignLinkQueryConditionData::make(
            campaignId: $this->campaignId(),
            link: $this->link(),
        )->toArray();
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => __mc('Clicked Any Link'),
                'none' => __mc('Did Not Click Any Link'),
                'equals' => __mc('Clicked A Specific Link'),
                'not-equals' => __mc('Did Not Click A Specific Link'),
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function render()
    {
        $this->options = self::getLinkClass()::query()
            ->whereHas('contentItem', function ($query) {
                $query
                    ->where('model_id', $this->campaignId())
                    ->where('model_type', (new (self::getCampaignClass()))->getMorphClass());
            })
            ->distinct()
            ->pluck('url')
            ->mapWithKeys(function (string $url) {
                return [$url => $url];
            })->toArray();

        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberClickedCampaignLinkCondition');
    }

    protected function campaignId(): ?int
    {
        return $this->storedCondition['value']['campaignId'] ?? null;
    }

    protected function link(): ?string
    {
        return $this->storedCondition['value']['link'] ?? null;
    }
}
