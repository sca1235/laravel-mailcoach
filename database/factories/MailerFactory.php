<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Settings\Enums\MailerTransport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class MailerFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getMailerClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'transport' => MailerTransport::Ses,
        ];
    }
}
