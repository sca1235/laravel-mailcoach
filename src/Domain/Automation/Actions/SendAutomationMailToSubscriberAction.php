<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): void
    {
        if (! $actionSubscriber->action->automation->repeat_enabled && $automationMail->contentItem->wasAlreadySentToSubscriber($actionSubscriber->subscriber)) {
            return;
        }

        $this
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->createSend($automationMail, $actionSubscriber);
    }

    protected function prepareEmailHtml(AutomationMail $automationMail): static
    {
        /** @var PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getSharedActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($automationMail->contentItem);

        return $this;
    }

    protected function prepareWebviewHtml(AutomationMail $automationMail): static
    {
        /** @var \Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getSharedActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($automationMail->contentItem);

        return $this;
    }

    protected function createSend(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): Send
    {
        if (! $actionSubscriber->action->automation->repeat_enabled) {
            /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $pendingSend */
            $pendingSend = $automationMail->contentItem->sends()
                ->where('subscriber_id', $actionSubscriber->subscriber->id)
                ->first();

            if ($pendingSend) {
                return $pendingSend;
            }
        }

        return $automationMail->contentItem->sends()->create([
            'subscriber_id' => $actionSubscriber->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
