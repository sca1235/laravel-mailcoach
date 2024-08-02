<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 *
 * @property string $uuid
 */
trait HasUuid
{
    public static $fakeUuid = null;

    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            /** @phpstan-ignore-next-line */
            $model->uuid = static::$fakeUuid ?? $model->uuid ?? (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function findByUuid($uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public static function firstOrFailByUuid($uuid): self
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
