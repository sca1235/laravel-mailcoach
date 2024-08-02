<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Ses\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSes;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSesConfig;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Ses\SesWebhookController;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class FeedbackStepComponent extends StepComponent
{
    use UsesMailer;

    public string $configurationType = 'automatic';

    public string $configurationName = 'mailcoach';

    public bool $trackOpens = false;

    public bool $trackClicks = false;

    public array $rules = [
        'configurationName' => ['required'],
    ];

    public function mount()
    {
        $this->trackOpens = $this->mailer()->get('open_tracking_enabled', false);
        $this->trackClicks = $this->mailer()->get('click_tracking_enabled', false);
    }

    public function setupFeedbackAutomatically()
    {
        $this->validate();

        $mailcoachSes = $this->getMailcoachSes();

        try {
            $mailcoachSes
                ->ensureValidAwsCredentials()
                ->deleteConfigurationSet()
                ->deleteSnsTopic()
                ->createConfigurationSet()
                ->createSnsTopic()
                ->createSnsSubscription()
                ->addSnsSubscriptionToSesTopic();
        } catch (Exception $e) {
            notifyError('Something went wrong while setting up SES feedback');
            $this->addError('configurationName', $e->getMessage());

            return;
        }

        $this->mailer()->merge([
            'ses_configuration_set' => $this->configurationName,
            'open_tracking_enabled' => $this->trackOpens,
            'click_tracking_enabled' => $this->trackClicks,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function setupFeedbackManually()
    {
        $this->validate();

        $this->mailer()->merge([
            'ses_configuration_set' => $this->configurationName,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('The settings have been saved.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.ses.feedback', [
            'mailer' => $this->mailer(),
        ]);
    }

    protected function getMailcoachSes(): MailcoachSes
    {
        $endpoint = action([SesWebhookController::class], $this->mailer()->configName());

        $sesConfig = new MailcoachSesConfig(
            $this->mailer()->get('ses_key'),
            $this->mailer()->get('ses_secret'),
            $this->mailer()->get('ses_region'),
            $endpoint,
        );

        $sesConfig->setConfigurationName($this->configurationName);

        if ($this->trackOpens) {
            $sesConfig->enableOpenTracking();
        }

        if ($this->trackClicks) {
            $sesConfig->enableClickTracking();
        }

        return new MailcoachSes($sesConfig);
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
