<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class AddUtmTagsToUrlAction
{
    public function execute(string $url, ContentItem $contentItem): string
    {
        if (str_starts_with($url, '::') || str_starts_with($url, '{{')) {
            return $url;
        }

        $tags = [
            'utm_source' => $contentItem->utm_source ?? 'newsletter',
            'utm_medium' => $contentItem->utm_medium ?? 'email',
            'utm_campaign' => $contentItem->utm_campaign ?? Str::slug($contentItem->model->name),
        ];

        $parsedUrl = parse_url($url);
        $parsedQuery = $tags;

        if (! isset($parsedUrl['host'])) {
            return $url;
        }

        if (! empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);
            foreach ($tags as $key => $value) {
                if (empty($parsedQuery[$key])) {
                    $parsedQuery[$key] = $value;
                }
            }
        }

        $query = http_build_query($parsedQuery);
        $path = $parsedUrl['path'] ?? '';

        $fragment = isset($parsedUrl['fragment']) ? "#{$parsedUrl['fragment']}" : '';

        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}?{$query}{$fragment}";
    }
}
