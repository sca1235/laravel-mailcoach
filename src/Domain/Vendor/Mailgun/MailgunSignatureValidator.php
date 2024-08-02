<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class MailgunSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|array',
            'signature.timestamp' => 'required|numeric',
            'signature.token' => 'required|string',
            'signature.signature' => 'required|string',
            'event-data' => 'required|array',
            'event-data.event' => 'required|string',
            'event-data.timestamp' => 'required|numeric',
            'event-data.id' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        return $request->input('signature.signature') === hash_hmac(
            'sha256',
            sprintf('%s%s', $request->input('signature.timestamp'), $request->input('signature.token')),
            $config->signingSecret
        );
    }
}
