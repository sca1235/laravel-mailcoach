<?php

namespace Spatie\Mailcoach\Livewire\Mailers;

use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Enums\MailerTransport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateMailerComponent extends Component
{
    use UsesMailcoachModels;

    public string $name = '';

    public string $transport = '';

    public function mount()
    {
        $this->transport = array_key_first($this->getTransportOptions());
    }

    public function saveMailer()
    {
        $this->validate([
            'name' => ['required', 'string'],
            'transport' => 'required',
        ]);

        $mailer = self::getMailerClass()::create([
            'name' => $this->name,
            'transport' => $this->transport,
        ]);

        notify(__mc('The mailer has been created.'));

        return redirect()->route('mailers.edit', $mailer);
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.partials.create', [
            'transports' => $this->getTransportOptions(),
        ]);
    }

    public function getTransportOptions(): array
    {
        return collect(MailerTransport::cases())
            ->mapWithKeys(fn (MailerTransport $transport) => [$transport->value => $transport->label()])
            /** @todo: Remove when package drops support for Laravel 10 */
            ->when(version_compare(app()->version(), '11.9.2', '<'), fn (Collection $collection) => $collection->reject(fn ($label) => $label === MailerTransport::Resend->label()))
            ->toArray();
    }
}
