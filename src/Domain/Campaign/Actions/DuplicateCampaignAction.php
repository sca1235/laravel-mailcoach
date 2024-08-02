<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DuplicateCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, ?string $newName = null): Campaign
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = self::getCampaignClass()::create([
            'name' => $newName ?? __mc('Duplicate of').' '.$campaign->name,
            'email_list_id' => $campaign->email_list_id,
            'segment_class' => $campaign->segment_class,
            'segment_id' => $campaign->segment_id,
        ]);

        $duplicateCampaign->contentItem->delete();

        $duplicateCampaign->load('contentItem');

        foreach ($campaign->contentItems as $contentItem) {
            $duplicateCampaign->contentItem()->create([
                'model_type' => $duplicateCampaign->getMorphClass(),
                'model_id' => $duplicateCampaign->id,
                'from_email' => $contentItem->from_email,
                'from_name' => $contentItem->from_name,
                'reply_to_email' => $contentItem->reply_to_email,
                'reply_to_name' => $contentItem->reply_to_name,
                'subject' => $contentItem->subject,
                'template_id' => $contentItem->template_id,
                'html' => $contentItem->html,
                'structured_html' => $contentItem->structured_html,
                'utm_tags' => (bool) $contentItem->utm_tags,
                'utm_source' => $contentItem->utm_source,
                'utm_medium' => $contentItem->utm_medium,
                'utm_campaign' => $contentItem->utm_campaign,
                'add_subscriber_tags' => $contentItem->add_subscriber_tags,
                'add_subscriber_link_tags' => $contentItem->add_subscriber_link_tags,
            ]);
        }

        $duplicateCampaign->update([
            'segment_description' => $duplicateCampaign->getSegment()->description(),
        ]);

        return $duplicateCampaign->refresh();
    }
}
