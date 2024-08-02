<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Vendor\Ses;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesWebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;

class SesWebhookController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request)
    {
        $this->registerMailerConfig($request->route('mailerConfigKey'));

        $webhookConfig = SesWebhookConfig::get();

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
