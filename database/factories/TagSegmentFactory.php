<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagSegmentFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return self::getTagSegmentClass();
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'email_list_id' => self::getEmailListClass()::factory(),
        ];
    }
}
