<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Spatie\Mailcoach\Database\Factories\AutomationMailFactory;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailTestJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Mailcoach;

class AutomationMail extends Sendable
{
    public $table = 'mailcoach_automation_mails';

    protected $casts = [
        'add_subscriber_tags' => 'boolean',
        'add_subscriber_link_tags' => 'boolean',
    ];

    public static function booted(): void
    {
        static::created(function (AutomationMail $automationMail) {
            if (! $automationMail->contentItem) {
                $contentItem = $automationMail->contentItem()->firstOrCreate();
                $automationMail->setRelation('contentItem', $contentItem);
            }
        });

        static::deleting(function (AutomationMail $automationMail) {
            $automationMail->contentItem->delete();
        });
    }

    /**
     * Returns a tuple with open & click tracking values
     */
    public function tracking(): array
    {
        $mailer = $this->getMailer();

        return [
            $mailer?->get('open_tracking_enabled', false),
            $mailer?->get('click_tracking_enabled', false),
        ];
    }

    public function isReady(): bool
    {
        return $this->contentItem->isReady();
    }

    public function send(ActionSubscriber $actionSubscriber): self
    {
        $this->ensureSendable();

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        dispatch(new SendAutomationMailToSubscriberJob($this, $actionSubscriber));

        return $this;
    }

    public function sendTestMail(string|array $emails, ?ContentItem $contentItem = null): void
    {
        if ($this->hasCustomMailable($contentItem)) {
            $this->pullSubjectFromMailable($contentItem);
        }

        collect($emails)->each(function (string $email) use ($contentItem) {
            (new SendAutomationMailTestJob($this, $email, $contentItem))->handle();
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.automations.webview', $this->uuid));
    }

    public function dispatchCalculateStatistics(): void
    {
        dispatch(new CalculateStatisticsJob($this));
    }

    protected static function newFactory(): AutomationMailFactory
    {
        return new AutomationMailFactory();
    }

    public function getMailerKey(): string
    {
        return Mailcoach::defaultAutomationMailer();
    }

    public function getMailer(): ?Mailer
    {
        $mailerClass = config('mailcoach.models.mailer');

        if (! class_exists($mailerClass)) {
            return null;
        }

        if (! $mailerKey = $this->getMailerKey()) {
            return null;
        }

        return $mailerClass::all()->first(fn ($mailerModel) => $mailerKey === $mailerModel->configName());
    }
}
