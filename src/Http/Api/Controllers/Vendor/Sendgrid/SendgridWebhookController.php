<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Vendor\Sendgrid;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\SendgridWebhookConfig;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\WebhookProcessor;

class SendgridWebhookController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request)
    {
        $this->registerMailerConfig($request->route('mailerConfigKey'));

        $webhookConfig = SendgridWebhookConfig::get();

        (new WebhookProcessor($request, $webhookConfig))->process();

        return response()->json(['message' => 'ok']);
    }

    public function registerMailerConfig(?string $mailer): void
    {
        if (! $mailer) {
            return;
        }

        $mailer = cache()->remember(
            "mailcoach-mailer-{$mailer}",
            now()->addMinute(),
            fn () => self::getMailerClass()::findByConfigKeyName($mailer),
        );

        $mailer?->registerConfigValues();
    }
}
