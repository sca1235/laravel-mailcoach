<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return static::getCampaignClass();
    }

    public function definition(): array
    {
        return [
            'status' => CampaignStatus::Draft,
            'uuid' => $this->faker->uuid,
            'email_list_id' => self::getEmailListClass()::factory(),
        ];
    }

    public function emptyDraft(?string $templateId = null): Factory
    {
        return $this->state(function (array $attributes) use ($templateId) {
            return [
                'from_email' => null,
                'from_name' => null,
                'reply_to_email' => null,
                'reply_to_name' => null,
                'template_id' => $templateId,
                'html' => null,
                'structured_html' => json_encode([
                    'templateValues' => [
                        'html' => null,
                    ],
                ], JSON_THROW_ON_ERROR),
            ];
        });
    }
}
