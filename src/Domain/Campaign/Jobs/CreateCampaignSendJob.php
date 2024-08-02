<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Mailcoach;

class CreateCampaignSendJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public $tries = 1;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->campaign->id}-{$this->subscriber->id}";
    }

    public function uniqueFor(): int
    {
        return $this->contentItem->sendTimeInMinutes() * 60;
    }

    public function __construct(
        protected Campaign $campaign,
        protected ContentItem $contentItem,
        protected Subscriber $subscriber
    ) {
        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle(): void
    {
        if ($this->campaign->isCancelled()) {
            return;
        }

        $pendingSend = $this->contentItem->sends()
            ->where('subscriber_id', $this->subscriber->id)
            ->exists();

        if ($pendingSend) {
            return;
        }

        $this->contentItem->sends()->create([
            'subscriber_id' => $this->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
