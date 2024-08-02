<?php

namespace Spatie\Mailcoach\Domain\Audience\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Validator;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Exceptions\CouldNotSubscribe;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class PendingSubscriber
{
    public bool $respectDoubleOptIn = true;

    public EmailList $emailList;

    public string $redirectAfterSubscribed = '';

    public ?array $tags = [];

    public bool $replaceTags = false;

    public ?CarbonInterface $subscribedAt = null;

    public ?CarbonInterface $unsubscribedAt = null;

    public array $extraAttributes = [];

    public function __construct(public string $email, public array $attributes = [])
    {
        if (Validator::make(compact('email'), ['email' => 'email'])->fails()) {
            throw CouldNotSubscribe::invalidEmail($email);
        }

        $this->email = strtolower($this->email);
    }

    public function withAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function withExtraAttributes(array $attributes): self
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function skipConfirmation(): self
    {
        $this->respectDoubleOptIn = false;

        return $this;
    }

    public function redirectAfterSubscribed(string $redirectUrl): self
    {
        $this->redirectAfterSubscribed = $redirectUrl;

        return $this;
    }

    public function tags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function replaceTags(bool $replaceTags = true): self
    {
        $this->replaceTags = $replaceTags;

        return $this;
    }

    public function appendTags(): self
    {
        $this->replaceTags = false;

        return $this;
    }

    public function subscribedAt(CarbonInterface $subscribedAt): self
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }

    public function unsubscribedAt(?CarbonInterface $unsubscribedAt): self
    {
        $this->unsubscribedAt = $unsubscribedAt;

        return $this;
    }

    public function subscribeTo(EmailList $emailList): Subscriber
    {
        $this->emailList = $emailList;

        /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction $createSubscriberAction */
        $createSubscriberAction = \Spatie\Mailcoach\Mailcoach::getAudienceActionClass('create_subscriber', CreateSubscriberAction::class);

        return $createSubscriberAction->execute($this);
    }
}
