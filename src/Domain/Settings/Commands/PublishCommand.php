<?php

namespace Spatie\Mailcoach\Domain\Settings\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishCommand extends Command
{
    public $signature = 'mailcoach:publish';

    public $description = 'Publish all assets needed by Mailcoach';

    public function handle(): void
    {
        $this->info('Publishing Mailcoach assets...');
        $this->info('');

        $commands = [
            'filament:upgrade',
            'horizon:publish',
            'vendor:publish --tag=mailcoach-migrations',
            'vendor:publish --tag=mailcoach-assets --force',
            'vendor:publish --tag=livewire:assets --force',
            'icons:cache',
        ];

        collect($commands)->each(function (string $command) {
            $this->comment("Executing `{$command}`...");

            try {
                Artisan::call($command, [], $this->output);
            } catch (Exception $exception) {
                $this->error("Error executing command: `{$exception->getMessage()}`");
            }

            $this->info('');
        });

        $this->info('All done!');
    }
}
