<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return static::getSendClass();
    }

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'content_item_id' => self::getContentItemClass()::factory(),
            'subscriber_id' => self::getSubscriberClass()::factory(),
        ];
    }

    public function automationMail(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'content_item_id' => self::getContentItemClass()::factory()->automationMail(),
            ];
        });
    }

    public function transactionalMailLogItem(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'content_item_id' => self::getContentItemClass()::factory()->transactionalMailLogItem(),
            ];
        });
    }
}
