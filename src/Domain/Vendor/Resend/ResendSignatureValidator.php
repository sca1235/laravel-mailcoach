<?php

namespace Spatie\Mailcoach\Domain\Vendor\Resend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class ResendSignatureValidator implements SignatureValidator
{
    private const int TOLERANCE = 5 * 60;

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $id = $request->header('svix-id') ?? $request->header('webhook-id');
        $timestamp = $request->header('svix-timestamp') ?? $request->header('message-timestamp');
        $signature = $request->header('svix-signature') ?? $request->header('message-signature');

        if (! $id || ! $timestamp || ! $signature) {
            return false;
        }

        $now = Date::now()->timestamp;
        $timestamp = (int) $timestamp;

        if ($timestamp < ($now - self::TOLERANCE)) {
            return false;
        }

        if ($timestamp > ($now + self::TOLERANCE)) {
            return false;
        }

        $payload = $request->getContent();

        $expectedSignature = $this->sign($id, $timestamp, $config->signingSecret, $payload);

        if ($expectedSignature === false) {
            return false;
        }

        $expectedSignature = explode(',', $expectedSignature, 2)[1];
        $passedSignatures = explode(' ', $signature);

        foreach ($passedSignatures as $versionedSignature) {
            $sigParts = explode(',', $versionedSignature, 2);
            [$version, $passedSignature] = $sigParts;

            if (strcmp($version, 'v1') !== 0) {
                continue;
            }

            if (hash_equals($expectedSignature, $passedSignature)) {
                return true;
            }
        }

        return false;
    }

    private function sign($id, int $timestamp, string $secret, string $payload): string|false
    {
        $isPositiveInt = ctype_digit($timestamp);

        if (! $isPositiveInt) {
            return false;
        }

        $secret = Str::after($secret, 'whsec_');
        $secret = base64_decode($secret);
        $hash = hash_hmac('sha256', "{$id}.{$timestamp}.{$payload}", $secret);
        $signature = base64_encode(pack('H*', $hash));

        return "v1,{$signature}";
    }
}
