<?php

namespace Spatie\Mailcoach\Domain\Audience\Support;

use Carbon\CarbonInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\ImportEmailHeader;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class ImportSubscriberRow
{
    protected array $values;

    public function __construct(protected EmailList $emailList, array $values)
    {
        $this->values = array_map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            }

            return $value;
        }, array_change_key_case($values, CASE_LOWER));
    }

    public function hasValidEmail(): bool
    {
        $validator = Validator::make(['email' => $this->getEmail()], ['email' => ['required', 'email:strict']]);

        return ! $validator->fails();
    }

    public function subscribedAt(): CarbonInterface
    {
        $date = $this->values['subscribed_at']
            ?? $this->values['optin_time']
            ?? $this->values['confirm_time']
            ?? $this->values['created_at']
            ?? null;

        if (! $date) {
            return now();
        }

        return Date::parse($date);
    }

    public function unsubscribedAt(): ?CarbonInterface
    {
        $date = $this->values['unsubscribed_at']
            ?? $this->values['unsub_time'] // Mailchimp
            ?? null;

        if (! $date) {
            return null;
        }

        return Date::parse($date);
    }

    public function hasUnsubscribed(): bool
    {
        $subscriptionStatus = $this->emailList->getSubscriptionStatus($this->getEmail());

        return $subscriptionStatus === SubscriptionStatus::Unsubscribed;
    }

    public function getAllValues(): array
    {
        return $this->values;
    }

    public function getEmail(): string
    {
        foreach (ImportEmailHeader::values() as $key) {
            if (array_key_exists($key, $this->values)) {
                return trim($this->values[$key]);
            }
        }

        return '';
    }

    public function getAttributes(): array
    {
        return [
            'first_name' => trim($this->values['first_name'] ?? $this->values['first name'] ?? ''),
            'last_name' => trim($this->values['last_name'] ?? $this->values['last name'] ?? ''),
        ];
    }

    public function getExtraAttributes(): array
    {
        return collect($this->values)
            ->reject(fn ($_value, string $key) => in_array($key, ['email', 'first_name', 'last_name', 'tags']))
            ->map(fn ($value, string $key) => [$key, $value])
            ->reduce(function (array $result, $keyValuePair) {
                [$key, $value] = $keyValuePair;

                $key = Str::replaceFirst('extra_attributes.', '', $key);

                $result[$key] = $value;

                return $result;
            }, []);
    }

    public function tags(): ?array
    {
        if (! array_key_exists('tags', $this->values)) {
            return null;
        }

        $tags = $this->values['tags'];

        if (is_array($tags)) {
            $tags = implode(';', $tags);
        }

        $delimiters = [';', ',', '|'];

        if ($tags instanceof DateTimeImmutable) {
            $tags = $tags->format('Y-m-d H:i:s');
        }

        /** Support any of the delimiters */
        $tags = str_replace($delimiters, $delimiters[0], $tags);
        $tags = str_replace(['"', "'"], '', $tags);

        $tags = explode(';', $tags);

        $sanitizedTags = array_map('trim', $tags);

        return array_filter($sanitizedTags);
    }
}
