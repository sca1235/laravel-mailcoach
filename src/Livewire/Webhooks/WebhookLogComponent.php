<?php

namespace Spatie\Mailcoach\Livewire\Webhooks;

use Illuminate\View\View;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookLogComponent extends Component
{
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public WebhookLog $webhookLog;

    public function mount(WebhookConfiguration $webhook, WebhookLog $webhookLog): void
    {
        $this->webhook = $webhook;
        $this->webhookLog = $webhookLog;
    }

    public function render(): View
    {
        return view('mailcoach::app.configuration.webhooks.logs.show')
            ->layout('mailcoach::app.layouts.settings', [
                'title' => __mc('Webhook Log Details'),
            ]);
    }

    public function getPrintableResponse(): string
    {
        $response = $this->webhookLog->response;

        // If the response is a string (likely a HTML response), we can show it as is.
        if (is_string($response)) {
            return $response;
        }

        return json_encode($response, JSON_PRETTY_PRINT);
    }
}
