<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Mailcoach\Database\Factories\TagFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @method static Builder|static query()
 */
class Tag extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_tags';

    public $guarded = [];

    protected $casts = [
        'visible_in_preferences' => 'bool',
        'type' => TagType::class,
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(self::getSubscriberClass(), 'mailcoach_email_list_subscriber_tags', 'tag_id', 'subscriber_id');
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function scopeEmailList(Builder $query, int|EmailList $emailList): void
    {
        if ($emailList instanceof EmailList) {
            $emailList = $emailList->id;
        }

        $query->where('email_list_id', $emailList);
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
