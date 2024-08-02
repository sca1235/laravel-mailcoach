<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasContentItems;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\InteractsWithContentItems;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Concerns\HasTemplate;

/**
 * @property ?string $name
 * @property string $html
 * @property ?string $email_html
 * @property ?string $webview_html
 * @property string $mailable_class
 * @property bool $utm_tags
 * @property ?string $subject
 * @property int $sent_to_number_of_subscribers
 * @property ?bool $disable_webview
 * @property ?\Spatie\Mailcoach\Domain\Content\Models\ContentItem $contentItem
 * @property-read ?CarbonInterface $updated_at
 */
abstract class Sendable extends Model implements HasContentItems
{
    use HasFactory;
    use HasTemplate;
    use HasUuid;
    use InteractsWithContentItems;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    protected $guarded = [];

    public $baseCasts = [
        'id' => 'int',
        'utm_tags' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'sent_at' => 'datetime',
        'requires_confirmation' => 'boolean',
        'statistics_calculated_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'mailable_arguments' => 'array',
    ];

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->contentItem->useMailable($mailableClass, $mailableArguments);

        return $this;
    }

    public function hasCustomMailable(?ContentItem $contentItem = null): bool
    {
        return $contentItem?->hasCustomMailable() ?? $this->contentItem->hasCustomMailable();
    }

    public function contentFromMailable(?ContentItem $contentItem = null): string
    {
        return $contentItem?->contentFromMailable() ?? $this->contentItem->contentFromMailable();
    }

    public function pullSubjectFromMailable(?ContentItem $contentItem = null): void
    {
        if ($contentItem) {
            $contentItem->pullSubjectFromMailable();

            return;
        }

        $this->contentItem->pullSubjectFromMailable();
    }

    public function htmlWithInlinedCss(?ContentItem $contentItem = null): string
    {
        return $contentItem?->htmlWithInlinedCss() ?? $this->contentItem->htmlWithInlinedCss();
    }

    public function from(string $email, ?string $name = null): static
    {
        $this->contentItem->from($email, $name);

        return $this;
    }

    public function replyTo(string $email, ?string $name = null): static
    {
        $this->contentItem->replyTo($email, $name);

        return $this;
    }

    public function subject(string $subject): static
    {
        $this->contentItem->setSubject($subject);

        return $this;
    }

    public function getFromEmail(?Send $send = null): string
    {
        return $this->contentItem->getFromEmail($send);
    }

    public function getFromName(?Send $send = null): ?string
    {
        return $this->contentItem->getFromName($send);
    }

    public function getReplyToEmail(?Send $send = null): ?string
    {
        return $this->contentItem->getReplyToEmail($send);
    }

    public function getReplyToName(?Send $send = null): ?string
    {
        return $this->contentItem->getReplyToName($send);
    }

    public function content(string $html): self
    {
        $this->ensureUpdatable();

        $this->contentItem->content($html);

        return $this;
    }

    public function htmlLinks(): Collection
    {
        return $this->contentItem->htmlLinks();
    }

    abstract public function isReady(): bool;

    public function isEditable(): bool
    {
        return true;
    }

    public function getCasts(): array
    {
        return array_merge($this->baseCasts, $this->casts);
    }

    protected function ensureSendable(): void
    {
    }

    abstract public function sendTestMail(string|array $emails, ?ContentItem $contentItem = null): void;

    abstract public function webviewUrl(): string;

    protected function ensureUpdatable(): void
    {
    }
}
