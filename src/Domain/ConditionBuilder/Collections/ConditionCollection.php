<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Collections;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberEmailQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedAutomationMailQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedCampaignQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberReceivedCampaignQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberSubscribedAtQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberTagsQueryCondition;

class ConditionCollection extends Collection
{
    public static function defaultConditions(): Collection
    {
        return collect([
            SubscriberClickedAutomationMailLinkQueryCondition::class,
            SubscriberClickedCampaignLinkQueryCondition::class,
            SubscriberEmailQueryCondition::class,
            SubscriberOpenedAutomationMailQueryCondition::class,
            SubscriberOpenedCampaignQueryCondition::class,
            SubscriberReceivedCampaignQueryCondition::class,
            SubscriberSubscribedAtQueryCondition::class,
            SubscriberTagsQueryCondition::class,
        ]);
    }

    public static function allConditions(): self
    {
        return new self(
            array_map(fn (string $class) => new $class(), config('mailcoach.audience.condition_builder_conditions'))
        );
    }

    public function options(): array
    {
        return $this
            ->map(fn (Condition $condition) => [
                'value' => $condition->key(),
                'label' => $condition->label(),
                'category' => $condition->category()->value,
            ])->toArray();
    }
}
