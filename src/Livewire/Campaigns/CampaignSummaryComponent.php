<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Actions\ResendCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\MainNavigation;

class CampaignSummaryComponent extends Component
{
    use AuthorizesRequests;

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        if ($campaign->status === CampaignStatus::Draft) {
            return redirect()->route('mailcoach.campaigns.delivery', $campaign);
        }

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns.summary', $campaign));
    }

    public function cancelSending(): void
    {
        $this->campaign->cancel();

        notify(__mc('Sending successfully cancelled.'));
    }

    public function resend()
    {
        $duplicatedCampaign = app(ResendCampaignAction::class)->execute($this->campaign);

        notify(__mc('Campaign successfully recreated for remaining subscribers.'));

        return redirect()->to(route('mailcoach.campaigns.summary', $duplicatedCampaign));
    }

    public function render()
    {
        $this->authorize('view', $this->campaign);

        return view('mailcoach::app.campaigns.summary', [
            'failedSendsCount' => $this->campaign->contentItems->sum(fn ($contentItem) => $contentItem->sends()->failed()->count()),
        ])->layout('mailcoach::app.campaigns.layouts.campaign', [
            'campaign' => $this->campaign,
            'title' => __mc('Performance'),
        ]);
    }
}
