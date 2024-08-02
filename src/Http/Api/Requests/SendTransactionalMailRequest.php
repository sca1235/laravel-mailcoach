<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Concerns\ValidatesAttributes;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Rules\MailerConfigKeyNameRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\ValidationRules\Rules\Delimited;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class SendTransactionalMailRequest extends FormRequest
{
    use UsesMailcoachModels;
    use ValidatesAttributes;

    public function rules(): array
    {
        return [
            'mail_name' => ['string', Rule::exists(self::getTransactionalMailClass(), 'name')],
            'subject' => ['nullable', 'string', 'required_without:mail_name'],
            'html' => ['string'],
            'replacements' => ['nullable', 'array'],
            'replacements.*' => ['nullable', function (string $attribute, $value, $fail) {
                if (is_string($value) || is_array($value)) {
                    return;
                }

                return $fail($attribute.__mc(' must be a string or array'));
            }],
            'from' => ['required'],
            'to' => [
                'required',
                (new Delimited('string'))->min(1),
                function (string $attribute, $value, $fail) {
                    try {
                        /** @var Address[] $addresses */
                        $addresses = (new AddressNormalizer())->normalize($value);

                        foreach ($addresses as $address) {
                            if (! $this->validateEmail('to', $address->getAddress(), ['rfc', 'strict'])) {
                                return $fail(__mc("{$address->getAddress()} is not a valid email."));
                            }
                        }
                    } catch (RfcComplianceException $exception) {
                        return $fail($exception->getMessage());
                    }
                },
            ],
            'cc' => [
                'nullable',
                (new Delimited('string'))->min(1),
                function (string $attribute, $value, $fail) {
                    try {
                        (new AddressNormalizer())->normalize($value);
                    } catch (RfcComplianceException $exception) {
                        $fail($exception->getMessage());
                    }
                },
            ],
            'bcc' => [
                'nullable',
                (new Delimited('string'))->min(1),
                function (string $attribute, $value, $fail) {
                    try {
                        (new AddressNormalizer())->normalize($value);
                    } catch (RfcComplianceException $exception) {
                        $fail($exception->getMessage());
                    }
                },
            ],
            'reply_to' => [
                'nullable',
                (new Delimited('string'))->min(1),
                function (string $attribute, $value, $fail) {
                    try {
                        (new AddressNormalizer())->normalize($value);
                    } catch (RfcComplianceException $exception) {
                        $fail($exception->getMessage());
                    }
                },
            ],
            'store' => ['boolean'],
            'mailer' => ['string', new MailerConfigKeyNameRule()],
            'attachments' => ['array', 'nullable'],
            'attachments.*.name' => ['required', 'string'],
            'attachments.*.content' => ['required', 'string'],
            'attachments.*.content_type' => ['required', 'string'],
            'attachments.*.content_id' => ['nullable', 'string'],
            'fake' => ['boolean'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::debug('Sending transactional mail validation failed.', [
            'errors' => $validator->errors(),
            'input' => $this->all(),
        ]);

        parent::failedValidation($validator);
    }

    public function replacements(): array
    {
        return $this->get('replacements', []);
    }

    public function attachments(): array
    {
        return $this->get('attachments', []);
    }

    public function shouldStoreMail(): bool
    {
        if (! $this->has('store')) {
            return true;
        }

        return (bool) $this->store;
    }

    public function getFromEmail(): ?string
    {
        $address = (new AddressNormalizer())->normalize($this->from)[0] ?? null;

        if (! $address) {
            return null;
        }

        return $address->getAddress();
    }

    public function getToEmails(): array
    {
        $addresses = (new AddressNormalizer())->normalize($this->to);

        return array_map(fn (Address $address) => $address->getAddress(), $addresses);
    }
}
