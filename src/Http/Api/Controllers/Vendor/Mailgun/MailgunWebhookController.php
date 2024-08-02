<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Vendor\Mailgun;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\MailgunWebhookConfig;
use Spatie\WebhookClient\Exceptions\InvalidWebhookSignature;
use Spatie\WebhookClient\WebhookProcessor;

class MailgunWebhookController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request)
    {
        $this->registerMailerConfig($request->route('mailerConfigKey'));

        $webhookConfig = MailgunWebhookConfig::get();

        try {
            (new WebhookProcessor($request, $webhookConfig))->process();
        } catch (InvalidWebhookSignature $exception) {
            report($exception);

            return response()->json(['message' => $exception->getMessage()], 406);
        }

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
