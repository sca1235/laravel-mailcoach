<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CannotResendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberReceivedCampaignQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ResendCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign): Campaign
    {
        if (! $campaign->isCancelled()) {
            throw CannotResendCampaign::notCancelled($campaign);
        }

        $newName = __mc('Resend of').' '.$campaign->name;
        $duplicate = app(DuplicateCampaignAction::class)->execute($campaign, $newName);

        $segment = self::getTagSegmentClass()::create([
            'name' => __mc('Subscribers who did not yet receive campaign: :name', ['name' => $campaign->name]),
            'email_list_id' => $campaign->email_list_id,
            'stored_conditions' => StoredConditionCollection::make([
                StoredCondition::make(
                    key: SubscriberReceivedCampaignQueryCondition::KEY,
                    comparisonOperator: ComparisonOperator::NotEquals,
                    value: $campaign->id,
                ),
            ]),
        ]);

        $duplicate->fill([
            'segment_id' => $segment->id,
            'segment_class' => SubscribersWithTagsSegment::class,
            'segment_description' => $segment->description($campaign),
        ]);

        $duplicate->send();

        return $duplicate;
    }
}
