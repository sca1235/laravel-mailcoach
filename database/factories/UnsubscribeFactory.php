<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UnsubscribeFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getUnsubscribeClass();
    }

    public function definition()
    {
        return [
            'content_item_id' => self::getContentItemClass()::factory(),
            'subscriber_id' => self::getSubscriberClass()::factory(),
        ];
    }
}
