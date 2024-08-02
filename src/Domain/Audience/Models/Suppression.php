<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Database\Factories\SuppressionFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @method static Builder|static query()
 *
 * @property-read string $email
 */
class Suppression extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_suppressions';

    protected $guarded = [];

    public $casts = [
        'reason' => SuppressionReason::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function for(string $email, ?SuppressionReason $reason = null): self
    {
        return static::firstOrCreate([
            'email' => $email,
        ], [
            'reason' => $reason ?? SuppressionReason::hardBounce,
        ]);
    }

    public static function fromAdmin(string $email, ?SuppressionReason $reason = null): self
    {
        return static::firstOrCreate([
            'email' => $email,
        ], [
            'reason' => $reason ?? SuppressionReason::manual,
        ]);
    }

    protected static function newFactory(): SuppressionFactory
    {
        return new SuppressionFactory();
    }

    public static function attributesFields(): array
    {
        return [
            'email' => __('Email address'),
        ];
    }
}
