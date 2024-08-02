<?php

namespace Spatie\Mailcoach\Domain\Content\Models;

use Carbon\CarbonInterface;
use DOMElement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\ContentItemFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer as PersonalizedAutomationReplacer;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer as PersonalizedCampaignReplacer;
use Spatie\Mailcoach\Domain\Content\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Content\Exceptions\CouldNotSendMail;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Actions\CommaSeparatedEmailsToArrayAction;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Concerns\HasTemplate;
use Spatie\Mailcoach\Mailcoach;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * @method static Builder|static query()
 */
class ContentItem extends Model implements HasHtmlContent
{
    use HasFactory;
    use HasTemplate;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    protected $guarded = [];

    public $table = 'mailcoach_content_items';

    public $casts = [
        'id' => 'int',
        'utm_tags' => 'boolean',
        'add_subscriber_tags' => 'boolean',
        'add_subscriber_link_tags' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'statistics_calculated_at' => 'datetime',
        'mailable_arguments' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(self::getTemplateClass());
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function links(): HasMany
    {
        return $this->hasMany(static::getLinkClass(), 'content_item_id');
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(self::getClickClass(), self::getLinkClass());
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getOpenClass());
    }

    public function sends(): HasMany
    {
        return $this->hasMany(self::getSendClass());
    }

    public function sentSends(): HasMany
    {
        return $this->sends()->whereNotNull('sent_at');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(self::getUnsubscribeClass(), 'content_item_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(self::getSendFeedbackItemClass(), self::getSendClass())
            ->whereIn('type', [SendFeedbackType::Bounce, SendFeedbackType::SoftBounce]);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(self::getSendFeedbackItemClass(), self::getSendClass())
            ->where('type', SendFeedbackType::Complaint);
    }

    public function isReady(): bool
    {
        if (! $this->html) {
            return false;
        }

        if (! $this->subject) {
            return false;
        }

        return true;
    }

    public function getTemplateFieldValues(): array
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        return $structuredHtml['templateValues'] ?? [];
    }

    public function setTemplateFieldValues(array $fieldValues = []): self
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        $structuredHtml['templateValues'] = $fieldValues;

        $this->structured_html = json_encode($structuredHtml);

        return $this;
    }

    public function hasValidHtml(): bool
    {
        $valid = true;

        (new HtmlRule())->validate('html', $this->html, function (&$valid) {
            $valid = false;
        });

        return $valid;
    }

    public function htmlError(): ?string
    {
        $rule = new HtmlRule();
        $errorMessage = null;

        $rule->validate('html', $this->html, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        return $errorMessage;
    }

    public function htmlContainsUnsubscribeUrlPlaceHolder(): bool
    {
        return Str::contains($this->html, '::unsubscribeUrl::')
            || Str::contains($this->html, '::preferencesUrl::')
            || Str::contains($this->html, '{{ unsubscribeUrl }}')
            || Str::contains($this->html, '{{ preferencesUrl }}')
            || Str::contains($this->html, '{{unsubscribeUrl}}')
            || Str::contains($this->html, '{{preferencesUrl}}');
    }

    public function from(string $email, ?string $name = null)
    {
        $this->update([
            'from_email' => $email,
            'from_name' => $name,
        ]);

        return $this;
    }

    public function replyTo(string $email, ?string $name = null)
    {
        $this->update([
            'reply_to_email' => $email,
            'reply_to_name' => $name,
        ]);

        return $this;
    }

    public function setSubject(?string $subject): self
    {
        $this->update(compact('subject'));

        return $this;
    }

    public function getFromEmail(?Send $send = null): string
    {
        return $this->from_email
            ?? $this->model->emailList?->default_from_email
            ?? $send?->subscriber->emailList->default_from_email
            ?? config('mail.from.address');
    }

    public function getFromName(?Send $send = null): ?string
    {
        return $this->from_name
            ?? $this->model->emailList?->default_from_name
            ?? $send?->subscriber->emailList->default_from_name
            ?? config('mail.from.name');
    }

    public function getReplyToEmail(?Send $send = null): ?string
    {
        // make internal in v7?
        return $this->reply_to_email
            ?? $this->model->emailList?->default_reply_to_email
            ?? $send?->subscriber->emailList->default_reply_to_email
            ?? null;
    }

    public function getReplyToName(?Send $send = null): ?string
    {
        // make internal in v7?
        return $this->reply_to_name
            ?? $this->model->emailList?->default_reply_to_name
            ?? $send?->subscriber->emailList->default_reply_to_name
            ?? null;
    }

    public function getReplyToAddresses(?Send $send = null): array
    {
        return resolve(CommaSeparatedEmailsToArrayAction::class)
            ->execute($this->getReplyToEmail($send), $this->getReplyToName($send));
    }

    public function utmTags(bool $bool = true): self
    {
        $this->update(['utm_tags' => $bool]);

        return $this;
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        if (! is_a($mailableClass, MailcoachMail::class, true)) {
            throw CouldNotSendMail::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass, 'mailable_arguments' => $mailableArguments]);

        return $this;
    }

    public function content(string $html): self
    {
        $this->update(compact('html'));

        return $this;
    }

    public function contentFromMailable(): string
    {
        return $this
            ->getMailable()
            ->setContentItem($this)
            ->render();
    }

    public function pullSubjectFromMailable(): void
    {
        if (! $this->hasCustomMailable()) {
            return;
        }

        $mailable = $this->getMailable()->setContentItem($this);
        $mailable->build();

        if (! empty($mailable->subject)) {
            $this->setSubject($mailable->subject);
        }
    }

    public function wasAlreadySentToSubscriber(Subscriber $subscriber): bool
    {
        return $this->sends()->whereNotNull('sent_at')->where('subscriber_id', $subscriber->id)->exists();
    }

    public function allSendsCreated(): bool
    {
        return ! is_null($this->all_sends_created_at);
    }

    public function markAsAllSendsCreated(): self
    {
        $this->update([
            'all_sends_created_at' => now(),
        ]);

        return $this;
    }

    public function allMailSendingJobsDispatched(): bool
    {
        return ! is_null($this->all_sends_dispatched_at);
    }

    public function markAsAllMailSendingJobsDispatched(): self
    {
        $this->update([
            'all_sends_dispatched_at' => now(),
        ]);

        return $this;
    }

    public function sendTimeInMinutes(): int
    {
        $mailer = $this->getMailerKey();
        $mailsPerTimespan = config("mail.mailers.{$mailer}.mails_per_timespan", 10);
        $timespan = config("mail.mailers.{$mailer}.timespan_in_seconds", 1);
        $mailsPerSecond = $mailsPerTimespan / $timespan;

        return (int) round($this->sent_to_number_of_subscribers / $mailsPerSecond / 60);
    }

    public function getReplacers(): Collection
    {
        return match (true) {
            $this->model instanceof Campaign => collect(config('mailcoach.campaigns.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof CampaignReplacer || $class instanceof PersonalizedCampaignReplacer),
            $this->model instanceof AutomationMail => collect(config('mailcoach.automation.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof AutomationMailReplacer || $class instanceof PersonalizedAutomationReplacer),
            default => collect(),
        };
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    public function sendsCount(): int
    {
        return $this->sends()->whereNotNull('sent_at')->count();
    }

    public function sendsWithErrors(): HasMany
    {
        return $this->sends()->whereNotNull('failed_at');
    }

    public function getStatsBefore(?CarbonInterface $dateTime = null): array
    {
        $dateTime ??= now();

        return Cache::remember(
            "content-item-{$this->id}-stats-before-{$dateTime->format('Y-m-d H:i:s')}",
            now()->addMonth(),
            function () use ($dateTime) {
                $sentToNumberOfSubscribers = $this->sends()->where('sent_at', '<=', $dateTime)->count();
                $sendIds = $this->sends()->where('sent_at', '<=', $dateTime)->select(self::getSendTableName().'.id');

                $openCount = $this->opens()->whereIn('send_id', $sendIds)->count();
                $uniqueOpenCount = $this->opens()->groupBy('subscriber_id')->whereIn('send_id', $sendIds)->toBase()->select('subscriber_id')->getCountForPagination(['subscriber_id']);
                $clickCount = $this->clicks()->whereIn('send_id', $sendIds)->count();
                $uniqueClickCount = $this->clicks()->groupBy('subscriber_id')->whereIn('send_id', $sendIds)->toBase()->select('subscriber_id')->getCountForPagination(['subscriber_id']);
                $unsubscribeCount = $this->unsubscribes()->where('created_at', '<=', $dateTime)->count();
                $bounceCount = $this->bounces()->whereIn('send_id', $sendIds)->count();

                return [
                    'sent_to_number_of_subscribers' => $sentToNumberOfSubscribers,
                    'open_count' => $openCount,
                    'unique_open_count' => $uniqueOpenCount,
                    'open_rate' => $sentToNumberOfSubscribers
                        ? round($uniqueOpenCount / $sentToNumberOfSubscribers, 4) * 10000
                        : 0,
                    'click_count' => $clickCount,
                    'unique_click_count' => $uniqueClickCount,
                    'click_rate' => $sentToNumberOfSubscribers
                        ? round($uniqueClickCount / $sentToNumberOfSubscribers, 4) * 10000
                        : 0,
                    'unsubscribe_count' => $unsubscribeCount,
                    'unsubscribe_rate' => $sentToNumberOfSubscribers
                        ? round($unsubscribeCount / $sentToNumberOfSubscribers, 4) * 10000
                        : 0,
                    'bounce_count' => $bounceCount,
                    'bounce_rate' => $sentToNumberOfSubscribers
                        ? round($bounceCount / $sentToNumberOfSubscribers, 4) * 10000
                        : 0,
                ];
            }
        );
    }

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === MailcoachMail::class) {
            return false;
        }

        return ! is_null($this->mailable_class);
    }

    public function htmlWithInlinedCss(): string
    {
        $html = $this->getHtml();

        if ($this->hasCustomMailable()) {
            $html = $this->contentFromMailable();
        }

        if (empty($html)) {
            return $html;
        }

        return (new CssToInlineStyles())->convert($html);
    }

    public function htmlLinks(): Collection
    {
        if ($this->getHtml() === '') {
            return collect();
        }

        $dom = app(CreateDomDocumentFromHtmlAction::class)->execute($this->getHtml());

        return collect($dom->getElementsByTagName('a'))
            ->map(function (DOMElement $link): string {
                return $link->getAttribute('href');
            })->reject(function (string $url): bool {
                return str_contains($url, '::') || str_contains($url, '{{');
            })
            ->reject(fn (string $url) => empty($url))
            ->unique();
    }

    public function getHtml(): string
    {
        return $this->html ?? '';
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function getStructuredHtml(): string
    {
        return $this->structured_html ?? '';
    }

    public function hasTemplates(): bool
    {
        return true;
    }

    public function sizeInKb(): int
    {
        return (int) ceil(mb_strlen($this->getHtml(), '8bit') / 1000);
    }

    public function getMailerKey(?Subscriber $subscriber = null): ?string
    {
        if ($this->model instanceof AutomationMail) {
            return $subscriber?->emailList->automation_mailer
                ?? Mailcoach::defaultAutomationMailer();
        }

        if ($this->model instanceof Campaign) {
            return $this->model->getMailerKey();
        }

        return Mailcoach::defaultTransactionalMailer();
    }

    protected static function newFactory(): ContentItemFactory
    {
        return new ContentItemFactory();
    }

    public function dispatchCalculateStatistics(): void
    {
        dispatch(new CalculateStatisticsJob($this));
    }
}
