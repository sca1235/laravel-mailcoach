<?php

namespace Spatie\Mailcoach\Domain\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\UnsubscribeFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Unsubscribe extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_unsubscribes';

    protected $guarded = [];

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(self::getContentItemClass(), 'content_item_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }

    protected static function newFactory(): UnsubscribeFactory
    {
        return new UnsubscribeFactory();
    }
}
