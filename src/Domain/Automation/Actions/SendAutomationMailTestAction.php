<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailTestAction
{
    use UsesMailcoachModels;

    public function execute(AutomationMail $mail, string $email, ?ContentItem $contentItem = null): void
    {
        $contentItem ??= $mail->contentItem;

        if (! $contentItem) {
            return;
        }

        $subject = $contentItem->subject;

        /** @var \Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getSharedActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($contentItem);

        /** @var \Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getSharedActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);
        $prepareWebviewHtmlAction->execute($contentItem);

        $contentItem->setSubject("[Test] {$subject}");

        if (! $subscriber = self::getSubscriberClass()::where('email', $email)->first()) {
            $subscriber = self::getSubscriberClass()::make([
                'uuid' => Str::uuid()->toString(),
                'email' => $email,
            ]);
        }

        $send = self::getSendClass()::make([
            'uuid' => Str::uuid()->toString(),
            'subscriber_id' => $subscriber->id,
            'content_item_id' => $contentItem->id,
        ]);
        $send->setRelation('subscriber', $subscriber);
        $send->setRelation('contentItem', $contentItem);

        try {
            /** @var \Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction $sendMailAction */
            $sendMailAction = Mailcoach::getSharedActionClass('send_mail', SendMailAction::class);
            $sendMailAction->execute($send, isTest: true);
        } finally {
            $contentItem->setSubject($subject);
            $send->delete();
        }
    }
}
