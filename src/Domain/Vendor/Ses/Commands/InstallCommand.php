<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSes;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSesConfig;

class InstallCommand extends Command
{
    public $signature = 'mailcoach:ses:install';

    public $description = 'Setup SES for use with Mailcoach';

    public function handle(): int
    {
        $this->info("Let's set up your AWS account!");

        $accessKey = $this->ask('Access Key Id?');
        $accessKeySecret = $this->ask('Access Key Secret?');
        $region = $this->ask('In which AWS region do you wish to send mails', 'eu-central-1');
        $configurationName = $this->ask('Which configuration name should we use', 'mailcoach');
        $endpoint = $this->ask('To which URL should SES send feedback (should start with https://)');
        $email = $this->ask('From which email address do you wish to send mails?');

        $config = new MailcoachSesConfig($accessKey, $accessKeySecret, $region, $endpoint);

        $config
            ->setConfigurationName($configurationName)
            ->sesIdentityEmail($email);

        (new MailcoachSes($config))->install();

        $this->info('SES was configured successfully!');

        return self::SUCCESS;
    }
}
