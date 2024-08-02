<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ClickFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getClickClass();
    }

    public function definition()
    {
        return [
            'send_id' => SendFactory::new(),
            'link_id' => self::getLinkClass()::factory(),
            'subscriber_id' => self::getSubscriberClass()::factory(),
        ];
    }
}
