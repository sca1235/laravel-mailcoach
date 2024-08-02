<?php

namespace Spatie\Mailcoach\Domain\Content\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\LinkFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Link extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_links';

    public $casts = [
        'click_count' => 'integer',
        'unique_click_count' => 'integer',
    ];

    protected $guarded = [];

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(self::getContentItemClass(), 'content_item_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(self::getClickClass());
    }

    public function registerClick(Send $send, ?DateTimeInterface $clickedAt = null): Click
    {
        /** @var \Spatie\Mailcoach\Domain\Content\Models\Click $click */
        $click = $this->clicks()->create([
            'send_id' => $send->id,
            'subscriber_id' => $send->subscriber?->id,
            'created_at' => $clickedAt ?? now(),
            'uuid' => Str::uuid(),
        ]);

        if ($send->subscriber) {
            $numberOfTimesClickedBySubscriber = $this->clicks()
                ->where('subscriber_id', $send->subscriber->id)
                ->count();

            if ($numberOfTimesClickedBySubscriber === 1) {
                $this->increment('unique_click_count');
            }
        } elseif ($this->unique_click_count === 0) {
            $this->increment('unique_click_count');
        }

        $this->increment('click_count');

        return $click;
    }

    protected static function newFactory(): LinkFactory
    {
        return new LinkFactory();
    }
}
