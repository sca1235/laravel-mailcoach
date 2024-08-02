<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Postmark\Steps;

use Illuminate\Support\Str;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Enums\PostMarkTrigger;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Postmark;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Postmark\PostmarkWebhookController;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class FeedbackStepComponent extends StepComponent
{
    use UsesMailer;

    public bool $trackOpens = false;

    public bool $trackClicks = false;

    public array $rules = [
        'trackOpens' => ['boolean'],
        'trackClicks' => ['boolean'],
    ];

    public function mount(): void
    {
        $this->trackOpens = $this->mailer()->get('open_tracking_enabled', false);
        $this->trackClicks = $this->mailer()->get('click_tracking_enabled', false);
    }

    public function configurePostmark(): void
    {
        $this->validate();

        $endpoint = action([PostmarkWebhookController::class], $this->mailer()->configName());

        $events = [PostMarkTrigger::Bounce, PostMarkTrigger::SpamComplaint];

        if ($this->trackOpens) {
            $events[] = PostMarkTrigger::Open;
        }

        if ($this->trackClicks) {
            $events[] = PostMarkTrigger::Click;
        }

        $secret = $this->mailer()->get('signing_secret', Str::random(20));

        $this->getPostmark()->configureWebhook(
            url: $endpoint,
            streamId: $this->mailer()->get('streamId'),
            triggers: $events,
            secret: $secret
        );

        $this->mailer()->merge([
            'open_tracking_enabled' => $this->trackOpens,
            'click_tracking_enabled' => $this->trackClicks,
            'signing_secret' => $secret,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.postmark.feedback');
    }

    protected function getPostmark(): Postmark
    {
        return new Postmark($this->mailer()->get('apiKey'));
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
