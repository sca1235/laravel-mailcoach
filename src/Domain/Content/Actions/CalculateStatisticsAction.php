<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class CalculateStatisticsAction
{
    use UsesMailcoachModels;

    public function execute(ContentItem $contentItem): void
    {
        if ($contentItem->getModel() instanceof TransactionalMail) {
            $this->calculateTransactionalStatistics($contentItem);
        } else {
            if ($contentItem->sends()->count() > 0) {
                $this
                    ->calculateStatistics($contentItem)
                    ->calculateLinkStatistics($contentItem);
            }
        }

        $contentItem->update(['statistics_calculated_at' => now()]);
        $contentItem->fresh('model');

        /** @var Campaign|AutomationMail|TransactionalMail $model */
        if (! $model = $contentItem->getModel()) {
            return;
        }

        match (true) {
            $model instanceof (static::getCampaignClass()) => event(new CampaignStatisticsCalculatedEvent($model)),
            $model instanceof (static::getAutomationMailClass()) => event(new AutomationMailStatisticsCalculatedEvent($model)),
            $model instanceof (static::getTransactionalMailClass()) => event(new TransactionalMailStatisticsCalculatedEvent($model)),
            default => null,
        };
    }

    protected function calculateStatistics(ContentItem $contentItem): self
    {
        $sentToNumberOfSubscribers = $contentItem->sends()->count();

        [$openCount, $uniqueOpenCount, $openRate] = $this->calculateOpenMetrics($contentItem, $sentToNumberOfSubscribers);
        [$clickCount, $uniqueClickCount, $clickRate] = $this->calculateClickMetrics($contentItem, $sentToNumberOfSubscribers);
        [$unsubscribeCount, $unsubscribeRate] = $this->calculateUnsubscribeMetrics($contentItem, $sentToNumberOfSubscribers);
        [$bounceCount, $bounceRate] = $this->calculateBounceMetrics($contentItem, $sentToNumberOfSubscribers);

        $contentItem->fill([
            'open_count' => $openCount,
            'unique_open_count' => $uniqueOpenCount,
            'open_rate' => $openRate,
            'click_count' => $clickCount,
            'unique_click_count' => $uniqueClickCount,
            'click_rate' => $clickRate,
            'unsubscribe_count' => $unsubscribeCount,
            'unsubscribe_rate' => $unsubscribeRate,
            'bounce_count' => $bounceCount,
            'bounce_rate' => $bounceRate,
        ]);

        if (! $contentItem->model instanceof (self::getCampaignClass())) {
            $contentItem->sent_to_number_of_subscribers = $sentToNumberOfSubscribers;
        }

        $contentItem->save();

        return $this;
    }

    protected function calculateLinkStatistics(ContentItem $contentItem): self
    {
        $contentItem->links()->each(function (Link $link) {
            $tableName = static::getClickTableName();

            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select("{$tableName}.subscriber_id")->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']),
            ]);
        });

        return $this;
    }

    protected function calculateClickMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $tableName = static::getClickTableName();

        $clickCount = $contentItem->clicks()->count();

        if ($contentItem->getModel() instanceof TransactionalMailLogItem) {
            $uniqueClickCount = $contentItem->clicks()->exists() ? 1 : 0;
        } else {
            $uniqueClickCount = $contentItem->clicks()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        }

        $clickRate = round($uniqueClickCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$clickCount, $uniqueClickCount, $clickRate];
    }

    protected function calculateOpenMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $tableName = static::getOpenTableName();

        $openCount = $contentItem->opens()->count();

        if ($contentItem->getModel() instanceof TransactionalMailLogItem) {
            $uniqueOpenCount = $contentItem->opens()->exists() ? 1 : 0;
        } else {
            $uniqueOpenCount = $contentItem->opens()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        }

        $openRate = round($uniqueOpenCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$openCount, $uniqueOpenCount, $openRate];
    }

    protected function calculateUnsubscribeMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $unsubscribeCount = $contentItem->unsubscribes()->count();
        $unsubscribeRate = round($unsubscribeCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$unsubscribeCount, $unsubscribeRate];
    }

    protected function calculateBounceMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $bounceCount = $contentItem->bounces()->distinct('send_id')->count();
        $bounceRate = round($bounceCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$bounceCount, $bounceRate];
    }

    protected function calculateTransactionalStatistics(ContentItem $contentItem): void
    {
        /** @var ?TransactionalMail $transactionalMail */
        $transactionalMail = $contentItem->getModel();

        if (! $transactionalMail) {
            return;
        }

        $stats = DB::table(self::getTransactionalMailLogItemTableName())
            ->where('mail_name', $transactionalMail->name)
            ->join(self::getContentItemTableName(), self::getContentItemTableName().'.model_id', '=', self::getTransactionalMailLogItemTableName().'.id')
            ->where(self::getContentItemTableName().'.model_type', (new (self::getTransactionalMailLogItemClass()))->getMorphClass())
            ->select([
                DB::raw('count(*) as sends_count'),
                DB::raw('SUM(open_count) as open_count'),
                DB::raw('SUM(unique_open_count) as unique_open_count'),
                DB::raw('SUM(click_count) as click_count'),
                DB::raw('SUM(unique_click_count) as unique_click_count'),
            ])
            ->first();

        if (! $stats) {
            return;
        }

        $openRate = round($stats->unique_open_count / $stats->sends_count, 4) * 10000;
        $clickRate = round($stats->unique_click_count / $stats->sends_count, 4) * 10000;

        $contentItem->fill([
            'sent_to_number_of_subscribers' => $stats->sends_count,
            'open_count' => $stats->open_count,
            'unique_open_count' => $stats->unique_open_count,
            'open_rate' => $openRate,
            'click_count' => $stats->click_count,
            'unique_click_count' => $stats->unique_click_count,
            'click_rate' => $clickRate,
        ]);
    }
}
