<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ActionSubscriberFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getActionSubscriberClass();
    }

    public function definition()
    {
        return [
            'subscriber_id' => self::getSubscriberClass()::factory(),
            'action_id' => self::getAutomationActionClass()::factory(),
        ];
    }
}
