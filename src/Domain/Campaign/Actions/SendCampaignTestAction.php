<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignTestAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, string $email, ?ContentItem $contentItem = null): void
    {
        $contentItem ??= $campaign->contentItem;

        if (! $contentItem) {
            return;
        }

        $originalUpdatedAt = $campaign->updated_at;
        $originalSubject = $contentItem->subject;
        $contentItem->setSubject("[Test] {$originalSubject}");

        /** @var \Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getSharedActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($contentItem);

        if (! $subscriber = self::getSubscriberClass()::where('email', $email)->where('email_list_id', $campaign->email_list_id)->first()) {
            $subscriber = self::getSubscriberClass()::make([
                'uuid' => Str::uuid()->toString(),
                'email_list_id' => $campaign->email_list_id,
                'email' => $email,
            ]);
        }

        $send = self::getSendClass()::make([
            'uuid' => Str::uuid()->toString(),
            'subscriber_id' => $subscriber->id,
            'content_item_id' => $contentItem->id,
        ]);
        $send->setRelation('subscriber', $subscriber);
        $send->setRelation('campaign', $campaign);

        try {
            /** @var \Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction $sendMailAction */
            $sendMailAction = Mailcoach::getSharedActionClass('send_mail', SendMailAction::class);
            $sendMailAction->execute($send, isTest: true);
        } finally {
            $contentItem->setSubject($originalSubject);
            $campaign->update([
                'updated_at' => $originalUpdatedAt,
            ]);
            $send->delete();
        }
    }
}
