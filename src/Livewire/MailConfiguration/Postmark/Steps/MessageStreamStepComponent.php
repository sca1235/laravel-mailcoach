<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Postmark\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Data\MessageStream;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Postmark;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class MessageStreamStepComponent extends StepComponent
{
    use UsesMailer;

    public string $streamId = '';

    public bool $streamsLoaded = false;

    public array $messageStreams = [];

    public $rules = [
        'streamId' => ['required'],
    ];

    public function mount(): void
    {
        $this->streamId = $this->mailer()->get('streamId', '');
    }

    public function submit(): void
    {
        $this->validate();

        $this->mailer()->merge([
            'streamId' => $this->streamId,
        ]);

        $this->nextStep();
    }

    public function loadStreams(): void
    {
        $postmark = (new Postmark($this->mailer()->get('apiKey')));
        $this->messageStreams = $postmark->getStreams()->mapWithKeys(fn (MessageStream $stream) => [$stream->id => $stream->name])->toArray();
        $this->streamsLoaded = true;
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Message Stream',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.postmark.messageStream');
    }
}
