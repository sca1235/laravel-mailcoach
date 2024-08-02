<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ActionFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return self::getAutomationActionClass();
    }

    public function definition()
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'automation_id' => self::getAutomationClass()::factory(),
            'order' => 0,
        ];
    }
}
