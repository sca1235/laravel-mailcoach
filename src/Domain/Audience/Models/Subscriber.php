<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Events\ResubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

/**
 * @method static Builder|static query()
 *
 * @property-read EmailList $emailList
 */
class Subscriber extends Model
{
    use HasExtraAttributes;
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_subscribers';

    protected $guarded = [];

    public $casts = [
        'extra_attributes' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $subscriber = parent::resolveRouteBinding($value, $field);

        if (! $subscriber && request('email_list_uuid')) {
            $subscriber = self::findForEmail($value, self::getEmailListClass()::findByUuid(request('email_list_uuid')));
        }

        return $subscriber;
    }

    public static function createWithEmail(string $email, array $attributes = []): PendingSubscriber
    {
        return new PendingSubscriber($email, $attributes);
    }

    public static function findForEmail(string $email, EmailList $emailList): ?Subscriber
    {
        return static::query()
            ->where('email_list_id', $emailList->id)
            ->where('email', $email)
            ->first();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(self::getSendClass(), 'subscriber_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getOpenClass(), 'subscriber_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(self::getClickClass(), 'subscriber_id');
    }

    public function uniqueClicks(): HasMany
    {
        return $this->clicks()->groupBy('link_id')->addSelect('link_id');
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id')
            ->orderBy('name');
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(self::getAutomationActionClass(), self::getActionSubscriberTableName())
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->withTimestamps();
    }

    public function currentAction(Automation $automation): ?Action
    {
        return $this->currentActions($automation)->first();
    }

    public function latestAction(Automation $automation): ?Action
    {
        return $this->actions()
            ->where('automation_id', $automation->id)
            ->latest()
            ->first();
    }

    public function currentActionClass(Automation $automation): ?string
    {
        if (! $action = $this->currentAction($automation)) {
            return null;
        }

        return $action->action::class;
    }

    public function currentActions(Automation $automation): Collection
    {
        return $this->actions()
            ->where('automation_id', $automation->id)
            ->wherePivotNull('completed_at')
            ->latest()
            ->get();
    }

    public function unsubscribe(?Send $send = null)
    {
        $this->update(['unsubscribed_at' => now()]);

        if ($send && $send->contentItem) {
            static::getUnsubscribeClass()::firstOrCreate([
                'content_item_id' => $send->contentItem->id,
                'subscriber_id' => $send->subscriber->id,
            ], ['uuid' => Str::uuid()]);

            $send->contentItem->dispatchCalculateStatistics();
        }

        event(new UnsubscribedEvent($this, $send));

        return $this;
    }

    public function resubscribe()
    {
        $this->update(['unsubscribed_at' => null]);

        event(new ResubscribedEvent($this));

        return $this;
    }

    public function unsubscribeUrl(?Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe', [$this->uuid, optional($send)->uuid]));
    }

    public function unsubscribeTagUrl(string $tag, ?Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe-tag', [$this->uuid, urlencode($tag), optional($send)->uuid]));
    }

    public function preferencesUrl(?Send $send = null): string
    {
        return url(route('mailcoach.manage-preferences', [$this->uuid, optional($send)->uuid]));
    }

    public function getStatusAttribute(): SubscriptionStatus
    {
        if (! is_null($this->unsubscribed_at)) {
            return SubscriptionStatus::Unsubscribed;
        }

        if (! is_null($this->subscribed_at)) {
            return SubscriptionStatus::Subscribed;
        }

        return SubscriptionStatus::Unconfirmed;
    }

    public function confirm()
    {
        $action = Mailcoach::getAudienceActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

        $action->execute($this);
    }

    public function scopeUnconfirmed(Builder $query)
    {
        $query
            ->whereNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeSubscribed(Builder $query)
    {
        $query
            ->whereNotNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $query)
    {
        $query
            ->whereNotNull('unsubscribed_at');
    }

    public function scopeWithoutSendsForCampaign(Builder $query, Campaign $campaign)
    {
        return $query->whereDoesntHave('sends', function (Builder $query) use ($campaign) {
            $query->whereIn('content_item_id', $campaign->contentItems->pluck('id'));
        });
    }

    public function scopeWithoutSendsForContentItems(Builder $query, array $contentItemIds)
    {
        return $query->whereDoesntHave('sends', function (Builder $query) use ($contentItemIds) {
            $query->whereIn('content_item_id', $contentItemIds);
        });
    }

    public function scopeEmailSearch(Builder $query, string $search, ?int $emailListId = null)
    {
        return $query
            ->when($emailListId, fn (Builder $query) => $query->where('email_list_id', $emailListId))
            ->where('email', 'like', "%{$search}%");
    }

    public function addTag(string|iterable $name, ?TagType $type = null): self
    {
        $names = Arr::wrap($name);

        return $this->addTags($names, $type);
    }

    public function addTags(array $names, ?TagType $type = null)
    {
        $this->load('tags');

        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $key = str($name)->trim()->lower()->ascii()->toString();

            $tag = Cache::lock("tag-{$key}", 10)->block(5, function () use ($name, $type) {
                return self::getTagClass()::firstOrCreate([
                    'name' => $name,
                    'email_list_id' => $this->emailList->id,
                ], [
                    'uuid' => Str::uuid(),
                    'type' => $type ?? TagType::Default,
                ]);
            });

            try {
                $this->tags()->attach($tag);
                $this->tags->add($tag);

                event(new TagAddedEvent($this, $tag));
            } catch (UniqueConstraintViolationException) {
                // Tag was already added
            }
        }

        return $this;
    }

    public function hasTag(string $name): bool
    {
        return $this->tags
            ->filter(function (Tag $tag) use ($name) {
                return str($tag->name)->trim()->lower()->ascii()->toString() === str($name)->trim()->lower()->ascii()->toString();
            })
            ->where('email_list_id', $this->emailList->id)
            ->count() > 0;
    }

    public function removeTag(string|array $name): self
    {
        $names = Arr::wrap($name);

        return $this->removeTags($names);
    }

    public function removeTags(array $names)
    {
        $tags = $this->tags()->whereIn('name', $names)->get();

        if ($tags->isEmpty()) {
            return $this;
        }

        foreach ($tags as $tag) {
            event(new TagRemovedEvent($this, $tag));
        }

        $this->tags()->detach($tags->pluck('id'));

        $this->load('tags');

        return $this;
    }

    public function syncTags(?array $names, string $type = 'default')
    {
        $names ??= [];

        $names = array_map('strtolower', $names);

        $this->addTags($names);

        $this->tags()->where('type', $type)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', $type)->whereNotIn('name', $names)->pluck(self::getTagTableName().'.id'));

        return $this;
    }

    public function syncPreferenceTags(?array $names)
    {
        $names ??= [];

        $this->addTags($names);

        $this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->pluck(self::getTagTableName().'.id'));

        return $this->fresh('tags');
    }

    public function toExportRow(): array
    {
        $attributes = $this->extra_attributes->toArray();
        ksort($attributes);

        return array_merge([
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'tags' => $this->tags->where('type', TagType::Default)->pluck('name')->unique()->implode(';'),
            'subscribed_at' => $this->subscribed_at?->format('Y-m-d H:i:s'),
            'unsubscribed_at' => $this->unsubscribed_at?->format('Y-m-d H:i:s'),
        ], $attributes);
    }

    public function isUnconfirmed(): bool
    {
        return $this->status === SubscriptionStatus::Unconfirmed;
    }

    public function isSubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Subscribed;
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Unsubscribed;
    }

    public function inAutomation(Automation $automation): bool
    {
        return $this->actions()->where('automation_id', $automation->id)->count() > 0;
    }

    protected static function newFactory(): SubscriberFactory
    {
        return new SubscriberFactory();
    }

    public static function attributesFields(): array
    {
        return [
            'first_name' => __('First name'),
            'last_name' => __('Last name'),
            'email' => __('Email address'),
        ];
    }

    public static function defaultActions(): Collection
    {
        return collect([
            'confirm_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction::class,
            'create_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction::class,
            'delete_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction::class,
            'import_subscribers' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscribersAction::class,
            'import_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction::class,
            'send_confirm_subscriber_mail' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction::class,
            'update_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction::class,
        ]);
    }
}
