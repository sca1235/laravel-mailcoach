<?php

namespace Spatie\Mailcoach\Domain\Content\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Content\Actions\CalculateStatisticsAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Mailcoach;
use Throwable;

class CalculateStatisticsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 60;

    public function __construct(public ContentItem $contentItem)
    {
        $this->queue = config('mailcoach.perform_on_queue.calculate_statistics_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function uniqueId()
    {
        return $this->contentItem->uuid;
    }

    public function handle(): void
    {
        try {
            /** @var \Spatie\Mailcoach\Domain\Content\Actions\CalculateStatisticsAction $calculateStatistics */
            $calculateStatistics = Mailcoach::getSharedActionClass('calculate_statistics', CalculateStatisticsAction::class);

            $calculateStatistics->execute($this->contentItem);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
