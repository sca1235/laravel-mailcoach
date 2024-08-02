<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\SchemalessAttributes;

trait ReplacesModelAttributes
{
    public function replaceModelAttributes(string $text, string $replaceText, Model $model): ?string
    {
        return preg_replace_callback('/(::|%3A%3A)'.$replaceText.'\.([\w.]+)(::|%3A%3A)/', function (array $match) use ($model) {
            $parts = collect(explode('.', $match[2] ?? ''));

            $replace = $parts->reduce(function ($value, $part) use ($model) {
                if ($value instanceof SchemalessAttributes) {
                    return $value->get($part) ?? '';
                }

                $result = $value->$part ?? $value[$part] ?? '';

                if (! $result && method_exists($model, 'getExtraAttributesAttribute')) {
                    $attributes = $model->getExtraAttributesAttribute();
                    $result = $attributes->get($part) ?? '';
                }

                return $result;
            }, $model);

            return $replace ?? $match;
        }, $text);
    }
}
