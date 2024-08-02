<?php

namespace Spatie\Mailcoach\Livewire\Webhooks;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Enums\WebhookEventTypes;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Webhooks\Forms\EditWebhookForm;

class EditWebhookComponent extends Component
{
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public EditWebhookForm $form;

    public array $emailLists;

    public array $eventOptions = [
    ];

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;
        $this->form->setWebhook($webhook);

        foreach (WebhookEventTypes::cases() as $eventType) {
            $this->eventOptions[$eventType->value()] = $eventType->label();
        }
    }

    public function save()
    {
        $this->form->store();

        notify(__mc('The webhook has been updated.'));
    }

    public function render()
    {
        $emailListNames = self::getEmailListClass()::query()
            ->pluck('name', 'id')
            ->toArray();

        return view('mailcoach::app.configuration.webhooks.edit', [
            'emailListNames' => $emailListNames,
        ])->layout('mailcoach::app.layouts.settings', [
            'title' => $this->webhook->name,
        ]);
    }
}
