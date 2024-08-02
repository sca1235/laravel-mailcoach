<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class OpenFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getOpenClass();
    }

    public function definition()
    {
        return [
            'send_id' => SendFactory::new(),
            'content_item_id' => self::getContentItemClass()::factory(),
            'subscriber_id' => self::getSubscriberClass()::factory(),
        ];
    }
}
