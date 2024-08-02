<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Actions\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\SuppressedEmail;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;
use Throwable;

class SendMailAction
{
    use UsesMailcoachModels;

    public function execute(Send $pendingSend, bool $isTest = false): void
    {
        try {
            $action = Mailcoach::getSharedActionClass('is_on_suppression_list', EnsureEmailsNotOnSuppressionListAction::class);

            $action->execute($pendingSend->subscriber->email);

            $this->sendMail($pendingSend, $isTest);
        } catch (SuppressedEmail) {
            $pendingSend->markAsSent();
            $pendingSend->registerSuppressed();

            return;
        } catch (Throwable|Exception $exception) {
            if ($isTest) {
                throw $exception;
            }

            /**
             * Postmark returns code 406 when you try to send
             * to an email that has been marked as inactive
             */
            if (
                str_contains($exception->getMessage(), '(code 406)')
                || str_contains($exception->getMessage(), "Invalid 'To' address")
                || str_contains($exception->getMessage(), "Error parsing 'To'")
            ) {
                // Mark as bounced
                $pendingSend->markAsSent();
                $pendingSend->registerBounce();

                return;
            }
            report($exception);

            $pendingSend->markAsFailed($exception->getMessage());
        }
    }

    protected function sendMail(Send $pendingSend, bool $isTest = false): void
    {
        if ($pendingSend->wasAlreadySent()) {
            return;
        }

        $contentItem = $pendingSend->contentItem;

        if (! $contentItem) {
            $pendingSend->delete();

            return;
        }

        if (! $pendingSend->subscriber) {
            $pendingSend->delete();

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction $personalizeTextAction */
        $personalizeTextAction = Mailcoach::getSharedActionClass('personalize_text', PersonalizeTextAction::class);

        $personalisedSubject = $personalizeTextAction->execute($contentItem->subject, $pendingSend);
        $personalisedHtml = $personalizeTextAction->execute(
            $contentItem->email_html,
            $pendingSend,
        );

        /** @var \Spatie\Mailcoach\Domain\Content\Actions\ConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = Mailcoach::getSharedActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        $mailcoachMail = resolve(MailcoachMail::class);

        $mailcoachMail
            ->setSend($pendingSend)
            ->setContentItem($contentItem)
            ->subject($personalisedSubject)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->withSymfonyMessage(function (Email $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('mailcoach-send-uuid', $pendingSend->uuid);
                $message->getHeaders()->addTextHeader('Precedence', 'Bulk');

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-Metadata-send-uuid', $pendingSend->uuid);
            });

        Mail::mailer($pendingSend->getMailerKey())
            ->to($pendingSend->subscriber->email)
            ->send($mailcoachMail);

        $pendingSend->markAsSent();

        if (! $isTest) {
            match (true) {
                $contentItem->model instanceof AutomationMail => event(new AutomationMailSentEvent($pendingSend)),
                $contentItem->model instanceof Campaign => event(new CampaignMailSentEvent($pendingSend)),
                default => null,
            };
        }
    }
}
