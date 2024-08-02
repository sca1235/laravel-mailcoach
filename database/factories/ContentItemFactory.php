<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ContentItemFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return static::getContentItemClass();
    }

    public function definition(): array
    {
        return [
            'model_type' => (new (self::getCampaignClass())())->getMorphClass(),
            'model_id' => self::getCampaignClass()::factory()->recycle($this),
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $html = $this->faker->randomHtml(),
            'structured_html' => json_encode([
                'templateValues' => [
                    'html' => json_encode($html),
                ],
            ], JSON_THROW_ON_ERROR),
            'uuid' => $this->faker->uuid,
            'template_id' => self::getTemplateClass()::factory(),
        ];
    }

    public function campaign(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'model_type' => (new (self::getCampaignClass())())->getMorphClass(),
                'model_id' => self::getCampaignClass()::factory()->recycle($this),
            ];
        });
    }

    public function automationMail(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'model_type' => (new (self::getAutomationMailClass())())->getMorphClass(),
                'model_id' => self::getAutomationMailClass()::factory()->recycle($this),
            ];
        });
    }

    public function transactionalMailLogItem(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'model_type' => (new (self::getTransactionalMailLogItemClass()))->getMorphClass(),
                'model_id' => self::getTransactionalMailLogItemClass()::factory()->recycle($this),
            ];
        });
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
