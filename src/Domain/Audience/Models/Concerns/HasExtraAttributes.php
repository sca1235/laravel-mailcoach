<?php

namespace Spatie\Mailcoach\Domain\Audience\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

trait HasExtraAttributes
{
    public function getExtraAttributesAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }
}
