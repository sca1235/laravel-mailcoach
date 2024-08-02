<?php

namespace Spatie\Mailcoach\Domain\Audience\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\Concerns\ReplacesPlaceholders;
use Spatie\Mailcoach\Domain\Content\Actions\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Domain\Shared\Actions\GetReplaceContextForSubscriberAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

class ConfirmSubscriberMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;
    use UsesMailcoachTemplate;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public string $confirmationUrl;

    public ?TransactionalMail $confirmationMailTemplate = null;

    public function __construct(Subscriber $subscriber, string $redirectAfterConfirmedUrl = '')
    {
        $this->subscriber = $subscriber;

        $this->confirmationUrl = url(route('mailcoach.confirm', $subscriber->uuid));

        $redirectAfterConfirmedUrl = empty($redirectAfterConfirmedUrl)
            ? $subscriber->emailList->redirect_after_subscribed ?? ''
            : $redirectAfterConfirmedUrl;

        if ($redirectAfterConfirmedUrl !== '') {
            $this->confirmationUrl .= "?redirect={$redirectAfterConfirmedUrl}";
        }
    }

    public function build()
    {
        $replacements = Arr::dot(array_filter(array_merge(app(GetReplaceContextForSubscriberAction::class)->execute($this->subscriber), [
            'confirmUrl' => $this->confirmationUrl,
        ])));

        $this->confirmationMailTemplate = $this->subscriber->emailList->confirmationMail;

        if ($this->confirmationMailTemplate) {
            $this->template($this->confirmationMailTemplate->name, $replacements);
        }

        $mail = $this
            ->from(
                $this->subscriber->emailList->default_from_email,
                $this->subscriber->emailList->default_from_name
            )
            ->determineSubject()
            ->determineContent();

        $mail->subject($this->replacePlaceholders($mail->subject));

        if ($this->confirmationMailTemplate) {
            $html = $this->confirmationMailTemplate->render($this, $replacements);

            $html = str_ireplace('::confirmUrl::', $this->confirmationUrl, $html);

            $html = $this->replacePlaceholders($html);

            $plaintext = Mailcoach::getSharedActionClass('convert_html_to_text', ConvertHtmlToTextAction::class)->execute($html);

            $this
                ->html($html)
                ->text('mailcoach::mails.transactionalMails.template', ['content' => $plaintext]);
        }

        if (! empty($this->subscriber->emailList->default_reply_to_email)) {
            foreach ($this->subscriber->emailList->defaultReplyTo() as $user) {
                $mail->replyTo($user['email'], $user['name']);
            }
        }

        $mail->withSymfonyMessage(function (Email $email) {
            $email->getHeaders()->addTextHeader('X-Mailgun-Track', 'no');
        });

        return $mail;
    }

    protected function determineSubject(): self
    {
        if ($this->confirmationMailTemplate) {
            return $this;
        }

        $this->subject(__mc('Confirm your subscription to :emailListName', ['emailListName' => $this->subscriber->emailList->name]));

        return $this;
    }

    protected function determineContent(): self
    {
        if ($this->confirmationMailTemplate) {
            return $this;
        }

        $this->markdown('mailcoach::mails.confirmSubscription');
        $this->text('mailcoach::mails.confirmSubscriptionText');

        return $this;
    }
}
