<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TransactionalMailLogItemFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getTransactionalMailLogItemClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'from' => [$this->person()],
            'to' => [$this->person()],
            'cc' => [$this->person()],
            'bcc' => [$this->person()],
            'mailable_class' => Mailable::class,
            'fake' => false,
        ];
    }

    protected function person(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }
}
