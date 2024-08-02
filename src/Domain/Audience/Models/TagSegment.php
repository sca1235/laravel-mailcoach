<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\ApplyConditionBuilderOnBuilderAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @method static Builder|static query()
 *
 * @property StoredConditionCollection $conditions
 */
class TagSegment extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_segments';

    public $casts = [
        'stored_conditions' => StoredConditionCollection::class,
    ];

    public $guarded = [];

    public function campaigns(): HasMany
    {
        return $this->hasMany(self::getCampaignClass(), 'segment_id');
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function getSubscribersQuery(): Builder
    {
        $query = $this->emailList->subscribers()->getQuery();

        $this->applyConditionBuilder($query);

        $query->where(self::getSubscriberTableName().'.email_list_id', $this->email_list_id);

        return $query;
    }

    public function getSubscribersCount(): int
    {
        return once(function () {
            return $this->getSubscribersQuery()->count();
        });
    }

    public function applyConditionBuilder(Builder $subscribersQuery): void
    {
        app(ApplyConditionBuilderOnBuilderAction::class)->execute(
            builder: $subscribersQuery,
            storedConditionCollection: $this->stored_conditions,
            tagSegment : $this
        );
    }

    public function description(Campaign $campaign): string
    {
        return $this->name;
    }

    protected static function newFactory(): TagSegmentFactory
    {
        return new TagSegmentFactory();
    }
}
