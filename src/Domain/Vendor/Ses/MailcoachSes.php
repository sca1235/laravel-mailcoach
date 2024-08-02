<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses;

use Aws\SesV2\Exception\SesV2Exception;
use Spatie\Mailcoach\Domain\Vendor\Ses\Exception\ConfigurationSetAlreadyExists;
use Spatie\Mailcoach\Domain\Vendor\Ses\Exception\InvalidAwsCredentials;

class MailcoachSes
{
    protected Aws $aws;

    protected MailcoachSesConfig $config;

    public function __construct(MailcoachSesConfig $setupConfig)
    {
        $this->aws = new Aws($setupConfig->key, $setupConfig->secret, $setupConfig->region);

        $this->config = $setupConfig;
    }

    public function install(): self
    {
        $this
            ->ensureValidAwsCredentials()
            ->ensureConfigurationSetDoesNotExistYet()
            ->createConfigurationSet()
            ->createSnsTopic()
            ->createSnsSubscription()
            ->addSnsSubscriptionToSesTopic()
            ->createSesIdentity();

        return $this;
    }

    public function verify(): self
    {
        $this
            ->ensureValidAwsCredentials();

        return $this;
    }

    public function uninstall(): self
    {
        $this
            ->ensureValidAwsCredentials()
            ->deleteConfigurationSet()
            ->deleteSnsTopic();

        return $this;
    }

    public function isInSandboxMode(): bool
    {
        return $this->aws()->getAwsAccount()->get('ProductionAccessEnabled') === false;
    }

    public function aws(): Aws
    {
        return $this->aws;
    }

    public function ensureValidAwsCredentials(): self
    {
        try {
            $this->aws->getAwsAccount();
        } catch (SesV2Exception $exception) {
            throw InvalidAwsCredentials::make($exception, $this->config);
        }

        return $this;
    }

    public function ensureConfigurationSetDoesNotExistYet(): self
    {
        if ($this->aws->configurationSetExists($this->config->sesConfigurationName)) {
            throw ConfigurationSetAlreadyExists::make($this->config->sesConfigurationName);
        }

        return $this;
    }

    public function createConfigurationSet(): self
    {
        $this->aws->createConfigurationSet($this->config->sesConfigurationName);

        return $this;
    }

    public function createSnsTopic(): self
    {
        $this->aws->createSnsTopic($this->config->snsTopicName);

        return $this;
    }

    public function createSnsSubscription(): self
    {
        $arn = $this->aws->getSnsTopicArn($this->config->snsTopicName);

        $this->aws->createSnsSubscription(
            $arn,
            $this->config->snsSubscriptionProtocol,
            $this->config->snsSubscriptionEndpoint,
            $this->config->maxWebhookReceivesPerSecond,
        );

        return $this;
    }

    public function addSnsSubscriptionToSesTopic(): self
    {
        $arn = $this->aws->getSnsTopicArn($this->config->snsTopicName);

        $this->aws->createConfigurationSetEventDestination(
            $this->config->sesConfigurationName,
            $arn,
            array_unique($this->config->extraTrackingEvents),
        );

        return $this;
    }

    public function createSesIdentity(): self
    {
        if (! $email = $this->config->sesIdentifyEmail) {
            return $this;
        }

        $this->aws->addSesIdentity($email);

        return $this;
    }

    public function deleteConfigurationSet(): self
    {
        $this->aws->deleteConfigurationSet($this->config->sesConfigurationName);

        return $this;
    }

    public function deleteSnsTopic(): self
    {
        $this->aws->deleteSnsTopic($this->config->snsTopicName);

        return $this;
    }
}
