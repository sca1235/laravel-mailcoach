<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\Concerns;

use Exception;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Settings\Models\Setting;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

trait UsesSettings
{
    use UsesMailcoachModels;

    public function put(array $values): self
    {
        self::getSettingClass()::setValues($this->getKeyName(), $values);

        Cache::forget($this->getCacheKey());

        return $this;
    }

    public function merge(array $values): self
    {
        $allValues = array_merge($this->all(), $values);

        self::getSettingClass()::setValues($this->getKeyName(), $allValues);

        Cache::forget($this->getCacheKey());

        return $this;
    }

    public function all(): array
    {
        return $this->getSettings()?->allValues() ?? [];
    }

    public function empty(): self
    {
        self::getSettingClass()::where('key')->delete();

        Cache::forget($this->getCacheKey());

        return $this;
    }

    public function __get(string $property)
    {
        return $this->get($property);
    }

    public function get(string $property, mixed $default = null): mixed
    {
        try {
            return $this->getSettings()?->getValue($property) ?? $default;
        } catch (Exception) {
            return $default;
        }
    }

    public function getCacheKey(): string
    {
        return self::getSettingClass().$this->getKeyName();
    }

    public function getSettings(): ?Setting
    {
        $settings = Cache::rememberForever($this->getCacheKey(), function () {
            // We return 'none' if there are not settings, because null does not get cached
            return self::getSettingClass()::where('key', $this->getKeyName())->first() ?? 'none';
        });

        if ($settings === 'none') {
            return null;
        }

        return $settings;
    }

    abstract public function getKeyName(): string;
}
