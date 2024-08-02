<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

class TransactionalMailFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getTransactionalMailClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->slug,
            'from' => $this->faker->email,
            'to' => [$this->faker->email],
            'cc' => [$this->faker->email],
            'bcc' => [$this->faker->email],
            'type' => 'blade',
            'test_using_mailable' => TestMailableWithTemplate::class,
        ];
    }
}
