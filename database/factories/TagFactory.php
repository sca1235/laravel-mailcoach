<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return self::getTagClass();
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(asText: true),
        ];
    }
}
