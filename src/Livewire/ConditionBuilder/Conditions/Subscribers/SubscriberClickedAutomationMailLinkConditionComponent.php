<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\ConditionBuilder\Data\SubscriberClickedAutomationMailLinkQueryConditionData;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberClickedAutomationMailLinkConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public ?int $automationMailId = null;

    public ?string $link = null;

    public array $automationMails = [];

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->storedCondition['value']['automationMailId'] ??= null;
        $this->storedCondition['value']['link'] ??= null;

        $this->automationMailId = $this->automationMailId();
        $this->link = $this->link();
        $this->automationMails = self::getAutomationMailClass()::query()
            ->has('contentItem.links')
            ->pluck('id', 'name')
            ->mapWithKeys(function (string $id, string $name) {
                return [$id => $name];
            })->toArray();
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

    public function getValue(): mixed
    {
        return SubscriberClickedAutomationMailLinkQueryConditionData::make(
            automationMailId: $this->automationMailId(),
            link: $this->link(),
        )->toArray();
    }

    public function render()
    {
        $this->options = self::getLinkClass()::query()
            ->whereHas('contentItem', function ($query) {
                $query
                    ->where('model_id', $this->automationMailId())
                    ->where('model_type', (new (self::getAutomationMailClass()))->getMorphClass());
            })
            ->distinct()
            ->pluck('url')
            ->mapWithKeys(function (string $url) {
                return [$url => $url];
            })->toArray();

        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberClickedAutomationMailLinkCondition');
    }

    protected function automationMailId(): ?int
    {
        return $this->storedCondition['value']['automationMailId'] ?? null;
    }

    protected function link(): ?string
    {
        return $this->storedCondition['value']['link'] ?? null;
    }
}
