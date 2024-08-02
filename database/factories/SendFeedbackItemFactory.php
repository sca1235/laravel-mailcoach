<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFeedbackItemFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return static::getSendFeedbackItemClass();
    }

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'type' => SendFeedbackType::Bounce,
            'send_id' => self::getSendClass()::factory(),
            'extra_attributes' => null,
        ];
    }
}
