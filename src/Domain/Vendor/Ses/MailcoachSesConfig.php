<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses;

class MailcoachSesConfig
{
    public string $sesConfigurationName = 'mailcoach';

    public string $snsTopicName = 'mailcoach';

    public string $snsSubscriptionProtocol;

    public string $snsSubscriptionEndpoint;

    public int $maxWebhookReceivesPerSecond = 10;

    public array $extraTrackingEvents = [];

    public ?string $sesIdentifyEmail = null;

    public function __construct(
        public string $key,
        public string $secret,
        public string $region,
        string $endpoint = '',
    ) {
        $this->snsSubscriptionProtocol = 'https';
        $this->snsSubscriptionEndpoint = $endpoint;
    }

    public function setConfigurationName(string $name): self
    {
        $this->sesConfigurationName = $name;
        $this->snsTopicName = $name;

        return $this;
    }

    public function enableClickTracking(): self
    {
        $this->extraTrackingEvents[] = 'CLICK';

        return $this;
    }

    public function enableOpenTracking(): self
    {
        $this->extraTrackingEvents[] = 'OPEN';

        return $this;
    }

    public function sesIdentityEmail(string $email)
    {
        $this->sesIdentifyEmail = $email;

        return $this;
    }

    public function maxWebhooksReceivesPerSecond(int $max): self
    {
        $this->maxWebhookReceivesPerSecond = $max;

        return $this;
    }
}
