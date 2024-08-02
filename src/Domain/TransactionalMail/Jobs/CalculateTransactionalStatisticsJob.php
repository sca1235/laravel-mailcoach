<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Mailcoach;

class CalculateTransactionalStatisticsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    private CarbonInterface $now;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.perform_on_queue.schedule'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle(): void
    {
        $this->now = now();

        self::getTransactionalMailClass()::query()
            ->each(function (TransactionalMail $transactionalMail) {
                $hasRecentSends = self::getTransactionalMailLogItemClass()::query()
                    ->join(
                        self::getContentItemTableName(),
                        self::getContentItemTableName().'.model_id',
                        '=',
                        self::getTransactionalMailLogItemTableName().'.id'
                    )
                    ->where(
                        self::getContentItemTableName().'.model_type',
                        (new (self::getTransactionalMailLogItemClass()))->getMorphClass()
                    )
                    ->where('mail_name', $transactionalMail->name)
                    ->where(fn (Builder $query) => $query->where('open_count', '>', 0)->orWhere('click_count', '>', 0))
                    ->when(! is_null($transactionalMail->contentItem->statistics_calculated_at), function (Builder $query) use ($transactionalMail) {
                        $query->where(self::getTransactionalMailLogItemTableName().'.created_at', '>', $transactionalMail->contentItem->statistics_calculated_at);
                    })
                    ->exists();

                if (! $hasRecentSends) {
                    $transactionalMail->contentItem->update(['statistics_calculated_at' => $this->now]);

                    return;
                }

                $transactionalMail->contentItem->dispatchCalculateStatistics();
            });
    }
}
