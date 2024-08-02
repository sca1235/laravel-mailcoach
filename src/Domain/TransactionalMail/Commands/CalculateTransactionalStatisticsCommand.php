<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Jobs\CalculateTransactionalStatisticsJob;

class CalculateTransactionalStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-transactional-statistics';

    public $description = 'Calculate the statistics of transactional mails';

    public function handle()
    {
        $this->comment('Start calculating statistics...');

        dispatch(new CalculateTransactionalStatisticsJob());

        $this->comment('All done!');
    }
}
