<?php

namespace Spatie\Mailcoach\Domain\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\ClickFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Click extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_clicks';

    protected $guarded = [];

    protected $casts = [
        'first_clicked_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(self::getSendClass(), 'send_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(self::getLinkClass(), 'link_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }

    protected static function newFactory(): ClickFactory
    {
        return new ClickFactory();
    }
}
