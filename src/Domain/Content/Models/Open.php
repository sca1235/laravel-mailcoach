<?php

namespace Spatie\Mailcoach\Domain\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\OpenFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Open extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_opens';

    protected $guarded = [];

    protected $casts = [
        'first_opened_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(self::getSendClass(), 'send_id');
    }

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(self::getContentItemClass());
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }

    protected static function newFactory(): OpenFactory
    {
        return new OpenFactory();
    }
}
