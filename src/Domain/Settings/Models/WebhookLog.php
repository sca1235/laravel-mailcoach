<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookLog extends Model
{
    use HasFactory;
    use HasUuid;
    use MassPrunable;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_webhook_logs';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    public function webhookConfiguration(): BelongsTo
    {
        return $this->belongsTo(self::getWebhookConfigurationClass(), 'webhook_configuration_id');
    }

    public function wasSuccessful(): bool
    {
        if ($this->status_code >= 400) {
            return false;
        }

        return true;
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMonth());
    }
}
