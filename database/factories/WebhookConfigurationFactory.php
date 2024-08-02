<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookConfigurationFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getWebhookConfigurationClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'secret' => $this->faker->word,
            'use_for_all_lists' => true,
            'use_for_all_events' => true,
            'enabled' => true,
            'failed_attempts' => 0,
        ];
    }
}
