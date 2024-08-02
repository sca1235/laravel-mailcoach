<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\CouldNotFindTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

/** @mixin \Illuminate\Mail\Mailable */
trait UsesMailcoachTemplate
{
    use StoresMail;
    use UsesMailcoachModels;

    public function template(
        string $name,
        array $replacements = []
    ): self {
        /** @var TransactionalMail $template */
        $template = self::getTransactionalMailClass()::firstWhere('name', $name);

        if (! $template) {
            throw CouldNotFindTransactionalMail::make($name, $this);
        }

        $this->setSubject($template, $replacements);

        if (empty($this->from) && $template->from) {
            $this->from($template->from);
        }

        foreach ($template->to ?? [] as $to) {
            $this->to($to);
        }

        foreach ($template->cc ?? [] as $cc) {
            $this->cc($cc);
        }

        foreach ($template->bcc ?? [] as $bcc) {
            $this->bcc($bcc);
        }

        $content = $template->render($this, $replacements);

        $this->view('mailcoach::mails.transactionalMails.template', compact('content'));

        $this->withSymfonyMessage(function (Email $message) use ($name) {
            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_MAIL_NAME, $name);
        });

        return $this;
    }

    protected function executeReplacers(
        string $text,
        TransactionalMail $template,
        Mailable $mailable
    ): string {
        foreach ($template->replacers() as $replacer) {
            $text = $replacer->replace($text, $mailable, $template);
        }

        return $text;
    }

    protected function setSubject(TransactionalMail $template, array $replacements): void
    {
        $subject = ! empty($this->subject)
            ? $this->subject
            : $template->contentItem->subject ?? '';

        foreach ($replacements as $search => $replace) {
            if (! is_string($replace)) {
                continue;
            }

            $subject = str_replace("::{$search}::", $replace, $subject);
        }

        $subject = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class)->execute($subject, array_merge($replacements, $template->replacers()->toArray()));

        $this->subject($this->executeReplacers($subject, $template, $this));
    }

    protected static function testInstance(): self
    {
        $instance = new self();
        $template = $instance::getTransactionalMailClass()::first();
        $instance->subject($instance->executeReplacers($template->contentItem->subject, $template, $instance));

        return $instance;
    }
}
