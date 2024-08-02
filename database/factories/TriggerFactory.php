<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TriggerFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getAutomationTriggerClass();
    }

    public function definition()
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'trigger' => new SubscribedTrigger(),
        ];
    }
}
