<?php

namespace Spatie\Mailcoach\Domain\Vendor\Ses\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSes;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSesConfig;

class UninstallCommand extends Command
{
    public $signature = 'mailcoach:ses:uninstall';

    public $description = 'Remove the Mailcoach configuration from SES';

    public function handle(): int
    {
        $this->info("Let's setup your AWS account!");

        $accessKey = $this->ask('Access Key Id?');
        $accessKeySecret = $this->ask('Access Key Secret?');
        $region = $this->ask('In which AWS region do you wish to uninstall Mailcoach', 'eu-central-1');
        $configurationName = $this->ask('Which configuration name should be removed', 'mailcoach');

        $config = new MailcoachSesConfig($accessKey, $accessKeySecret, $region);

        $config->setConfigurationName($configurationName);

        (new MailcoachSes($config))->uninstall();

        $this->info('The Mailcoach configuration has been removed from SES');

        return self::SUCCESS;
    }
}
