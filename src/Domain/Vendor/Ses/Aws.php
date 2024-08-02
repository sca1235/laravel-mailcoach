<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses;

use Aws\Result;
use Aws\SesV2\Exception\SesV2Exception;
use Aws\SesV2\SesV2Client;
use Aws\Sns\SnsClient;

class Aws
{
    protected SesV2Client $ses;

    protected SnsClient $sns;

    public function __construct(
        protected string $key,
        protected string $secret,
        protected string $region
    ) {
        $this->ses = new SesV2Client([
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'region' => $region,
            'version' => '2019-09-27',
        ]);

        $this->sns = new SnsClient([
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'region' => $region,
            'version' => '2010-03-31',
        ]);
    }

    public function getAwsAccount(): Result
    {
        return $this->ses->getAccount();
    }

    public function configurationSetExists(string $name): bool
    {
        try {
            $this->ses->getConfigurationSet(['ConfigurationSetName' => $name]);
        } catch (SesV2Exception $exception) {
            return false;
        }

        return true;
    }

    public function createConfigurationSet(string $name): self
    {
        $this->ses->createConfigurationSet([
            'ConfigurationSetName' => $name,
        ]);

        return $this;
    }

    public function deleteConfigurationSet(string $name): self
    {
        try {
            $this->ses->deleteConfigurationSet([
                'ConfigurationSetName' => $name,
            ]);
        } catch (SesV2Exception $exception) {
            if ($exception->getAwsErrorCode() !== 'NotFoundException') {
                throw $exception;
            }
        }

        return $this;
    }

    public function createSnsTopic(string $name): string
    {
        $result = $this->sns->createTopic([
            'Name' => $name,
        ]);

        return $result->get('TopicArn');
    }

    public function snsTopicExists(string $name): bool
    {
        $arn = $this->getSnsTopicArn($name);

        return (bool) $arn;
    }

    public function deleteSnsTopic(string $name): self
    {
        $arn = $this->getSnsTopicArn($name);

        if (! $arn) {
            return $this;
        }

        $this->sns->deleteTopic([
            'TopicArn' => $arn,
        ]);

        return $this;
    }

    public function getSnsTopicArn(string $name): ?string
    {
        $result = $this->sns->listTopics([
            'Name' => $name,
        ]);

        foreach ($result->get('Topics') as $topic) {
            if (str_ends_with($topic['TopicArn'], ":{$name}")) {
                return $topic['TopicArn'];
            }
        }

        return null;
    }

    public function createSnsSubscription(
        string $snsTopicArn,
        string $protocol,
        string $endpoint,
        int $maxReceivesPerSecond
    ): self {
        $this->sns->subscribe([
            'TopicArn' => $snsTopicArn,
            'Protocol' => $protocol,
            'Endpoint' => $endpoint,
            'Attributes' => [
                'DeliveryPolicy' => json_encode([
                    'throttlePolicy' => [
                        'maxReceivesPerSecond' => $maxReceivesPerSecond,
                    ],
                ]),
            ],
        ]);

        return $this;
    }

    public function createConfigurationSetEventDestination(
        string $configurationName,
        $snsDestinationTopicArn,
        array $extraEvents = [],
    ): self {
        $config = [
            'ConfigurationSetName' => $configurationName,
            'EventDestination' => [
                'Enabled' => true,
                'MatchingEventTypes' => array_merge(
                    ['REJECT', 'BOUNCE', 'COMPLAINT'],
                    $extraEvents,
                ),
                'SnsDestination' => [
                    'TopicArn' => $snsDestinationTopicArn,
                ],
            ],
            'EventDestinationName' => 'mailcoach',
        ];

        $this->ses->createConfigurationSetEventDestination($config);

        return $this;
    }

    public function addSesIdentity(string $email): self
    {
        $this->ses->createEmailIdentity([
            'EmailIdentity' => $email,
        ]);

        return $this;
    }
}
