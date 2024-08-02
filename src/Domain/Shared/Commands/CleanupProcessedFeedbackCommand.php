<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Shared\Jobs\CleanupProcessedFeedbackJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CleanupProcessedFeedbackCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:cleanup-processed-feedback {--hours=1 : Processed feedback older than this value will be deleted}';

    public $description = 'Cleanup processed feedback';

    protected CarbonInterface $now;

    public function handle()
    {
        $hours = (int) $this->option('hours');
        dispatch(new CleanupProcessedFeedbackJob($hours));
    }
}
