<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SubscriberImportFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getSubscriberImportClass();
    }

    public function definition()
    {
        return [
            'status' => SubscriberImportStatus::Completed,
            'email_list_id' => self::getEmailListClass()::factory(),
            'imported_subscribers_count' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
