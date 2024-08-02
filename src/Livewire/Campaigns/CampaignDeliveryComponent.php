<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class CampaignDeliveryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public Campaign $campaign;

    public ?CarbonInterface $scheduled_at_date;

    public array $scheduled_at;

    public bool $readyToLoad = false;

    public int $split_test_wait_time_in_minutes;

    public int $split_test_split_size_percentage;

    public int $split_length;

    protected $listeners = [
        'send-campaign' => 'send',
    ];

    protected function rules(): array
    {
        return [
            'scheduled_at' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function mount(Campaign $campaign)
    {
        $this->authorize('send', $this->campaign);

        if (! $campaign->isEditable()) {
            return $this->redirect(route('mailcoach.campaigns.summary', $this->campaign));
        }

        $this->campaign = $campaign;
        $this->split_test_wait_time_in_minutes = $this->campaign->split_test_wait_time_in_minutes ?? 240;
        $this->split_test_split_size_percentage = $this->campaign->split_test_split_size_percentage ?? 30;

        $this->split_length = $this->split_test_wait_time_in_minutes / 60;

        $this->scheduled_at_date = $campaign->scheduled_at ?? now()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->addHour()->startOfHour();

        $this->scheduled_at = [
            'date' => $this->scheduled_at_date->format('Y-m-d'),
            'hours' => $this->scheduled_at_date->format('H'),
            'minutes' => $this->scheduled_at_date->format('i'),
        ];

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns'));
    }

    public function updatedScheduledAt()
    {
        $this->scheduled_at_date = (new DateTimeFieldRule())->parseDateTime($this->scheduled_at);
    }

    public function saveSplitTestSettings(): void
    {
        $this->validate([
            'split_length' => ['integer', 'min:1'],
            'split_test_split_size_percentage' => ['integer', 'min:1'],
        ]);

        $this->campaign->update([
            'split_test_split_size_percentage' => $this->split_test_split_size_percentage,
            'split_test_wait_time_in_minutes' => $this->split_length * 60,
        ]);

        notify(__mc('Split test settings updated.'));
    }

    public function unschedule()
    {
        $this->campaign->markAsUnscheduled();

        notify(__mc('Campaign :campaign was unscheduled', ['campaign' => $this->campaign->name]));
    }

    public function schedule()
    {
        $this->validate();

        if (! $this->campaign->isPending()) {
            notify(__mc('Campaign :campaign could not be scheduled because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->scheduleToBeSentAt($this->scheduled_at_date->setTimezone(config('mailcoach.timezone') ?? config('app.timezone')));

        notify(__mc('Campaign :campaign is scheduled for delivery.', ['campaign' => $this->campaign->name]));
    }

    public function send()
    {
        if (! $this->campaign->isPending()) {
            notify(__mc('Campaign :campaign could not be sent because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->send();

        notify(__mc('Campaign :campaign is being sent.', ['campaign' => $this->campaign->name]));

        return redirect()->route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.delivery',
            [
                'fromEmail' => $this->campaign->getFromEmail(),
                'fromName' => $this->campaign->getFromName(),
                'replyToEmail' => $this->campaign->getReplyToEmail(),
                'replyToName' => $this->campaign->getReplyToName(),
            ])
            ->layout('mailcoach::app.campaigns.layouts.campaign', [
                'campaign' => $this->campaign,
                'title' => __mc('Send'),
            ]);
    }
}
