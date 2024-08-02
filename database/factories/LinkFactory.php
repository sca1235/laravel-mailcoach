<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class LinkFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getLinkClass();
    }

    public function definition(): array
    {
        return [
            'content_item_id' => ContentItem::factory(),
            'url' => $this->faker->url,
        ];
    }
}
