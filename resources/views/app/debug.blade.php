@php($issueBody = "## Describe your issue\n\n\n\n---\n## Health check:\n\n")
<div class="card-grid form-fieldsets-no-max-w">
    <x-mailcoach::fieldset card :legend="__mc('Health')">
        @php($issueBody.='**Environment**: ' . app()->environment() . "\n")
        <table>
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Environment')"
                :test="app()->environment('production')"
                :value="app()->environment()"
            />
            @php($issueBody.='**Debug**: ' . (config('app.debug') ? 'ON' : 'OFF') . "\n")
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Debug')"
                :test="!config('app.debug')"
                :value="config('app.debug') ? __mc('ON') : __mc('OFF')"
            />
            @if(! $usesVapor)
                @php($issueBody.='**Horizon**: ' . ($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE) ? 'Active' : 'Inactive') . "\n")
                <x-mailcoach::checklist-item
                    :label="__mc('Horizon')"
                    :test="$horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE)"
                >
                    @if($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE))
                        {{ __mc('Active') }}
                    @else
                        {!! __mc('Horizon is inactive. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                </x-mailcoach::checklist-item>

                @php($issueBody.='**Queue** connection: ' . ($hasQueueConnection ? 'OK' : 'Not OK') . "\n")
                <x-mailcoach::checklist-item
                    :label="__mc('Queue connection')"
                    :test="$hasQueueConnection"
                >
                    @if($hasQueueConnection)
                        {!! __mc('Queue connection settings for <code>mailcoach-redis</code> exist.') !!}
                    @else
                        {!! __mc('No valid <strong>queue connection</strong> found. Configure a queue connection with the <strong>mailcoach-redis</strong> key. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                </x-mailcoach::checklist-item>
            @endif
            @php($issueBody.='**Webhooks**: ' . $webhookTableCount . " unprocessed webhooks\n")
            <x-mailcoach::checklist-item
                :label="__mc('Webhooks')"
                :test="$webhookTableCount === 0"
            >
                @if($webhookTableCount === 0)
                    {{ __mc('All webhooks are processed.') }}
                @else
                    {{ __mc(':count unprocessed webhooks.', ['count' => $webhookTableCount ]) }}
                @endif
            </x-mailcoach::checklist-item>

            @if ($lastScheduleRun && now()->diffInMinutes($lastScheduleRun, absolute: true) < 10)
                @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun, absolute: true) . " minute(s) ago\n")
            @elseif ($lastScheduleRun)
                @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun, absolute: true) . " minute(s) ago\n")
            @else
                @php($issueBody.="**Schedule**: hasn't run\n")
            @endif
            <x-mailcoach::checklist-item
                :test="$lastScheduleRun && now()->diffInMinutes($lastScheduleRun, absolute: true) < 10"
                :warning="$lastScheduleRun"
                :label="__mc('Schedule')"
            >
                @if ($lastScheduleRun)
                    {{ __mc('Ran :lastRun minute(s) ago.', ['lastRun' => now()->diffInMinutes($lastScheduleRun, absolute: true) ]) }}
                @else
                    {{ __mc('Schedule hasn\'t run.') }}
                @endif
            </x-mailcoach::checklist-item>
        </table>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Schedule')">
        @if ($scheduledJobs->count())
            <?php /** @var \Illuminate\Console\Scheduling\Event $scheduledJob */ ?>
            <table class="fi-ta fi-ta-table min-w-full border-separate border-spacing-0">
                <thead>
                <th class="w-36">{{ __mc('Schedule') }}</th>
                <th>{{ __mc('Command') }}</th>
                <th class="w-40">{{ __mc('Background') }}</th>
                <th class="w-40">{{ __mc('No overlap') }}</th>
                </thead>
                @foreach($scheduledJobs as $scheduledJob)
                    <tr class="fi-ta-row">
                        <td class="fi-ta-cell fi-ta-text-item px-3 py-4">
                            <code>
                                {!! str_replace(' ', '&nbsp;', $scheduledJob->expression) !!}
                            </code>
                        </td>
                        <td class="fi-ta-cell fi-ta-text-item px-3 py-4">
                            <code>
                                {{ \Illuminate\Support\Str::after($scheduledJob->command, '\'artisan\' ') }}
                            </code>
                        </td>
                        <td class="fi-ta-cell fi-ta-text-item px-3 py-4 text-center">
                            @if ($scheduledJob->runInBackground)
                                <x-mailcoach::rounded-icon type="success" icon="heroicon-s-check"/>
                            @else
                                <x-mailcoach::rounded-icon type="neutral" icon="heroicon-s-x-mark"/>
                            @endif
                        </td>
                        <td class="fi-ta-cell fi-ta-text-item px-3 py-4 text-center">
                            @if ($scheduledJob->withoutOverlapping)
                                <x-mailcoach::rounded-icon type="success" icon="heroicon-s-check"/>
                            @else
                                <x-mailcoach::rounded-icon type="neutral" icon="heroicon-s-x-mark"/>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <x-mailcoach::alert type="error">
                {{ __mc('No scheduled jobs!') }}
            </x-mailcoach::alert>
        @endif
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Filesystem configuration')">
        <table>
            @foreach($filesystems as $key => $filesystem)
                @php($issueBody.="**{$key} disk**: " . $filesystem['disk'] . " (visibility: " . $filesystem['visibility'] . ")\n")
                <x-mailcoach::checklist-item
                    :test="$filesystem['disk'] !== 'public' && $filesystem['visibility'] !== 'public'"
                    :label="$key"
                    neutral
                >
                    <code>
                        {{ $filesystem['disk'] }}
                    </code>
                    (visibility: {{ $filesystem['visibility'] }})
                </x-mailcoach::checklist-item>
            @endforeach
        </table>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Mailers')">
        <table>
            @php($issueBody.="**Default mailer**: " . config('mail.default') . "\n")
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Default mailer')"
                :test="!in_array(config('mail.default'), ['log', 'array', null])"
            >
                <code>{{ config('mail.default') }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**Mailcoach mailer**: " . (config('mailcoach.mailer') ?? 'null') . "\n")
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Mailcoach mailer')"
                :test="!in_array(config('mailcoach.mailer'), ['log', 'array'])"
            >
                <code>{{ config('mailcoach.mailer') ?? 'null' }}</code>
            </x-mailcoach::checklist-item>

            @php($issueBody.="**Campaign mailer**: " . (config('mailcoach.campaigns.mailer') ?? 'null') . "\n")
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Campaign mailer')"
                :test="!in_array(config('mailcoach.campaigns.mailer'), ['log', 'array'])"
            >
                <code>{{ config('mailcoach.campaigns.mailer') ?? 'null' }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**Transactional mailer**: " . (config('mailcoach.transactional.mailer') ?? 'null') . "\n")
            <x-mailcoach::checklist-item
                warning
                :label="__mc('Transactional mailer')"
                :test="!in_array(config('mailcoach.transactional.mailer'), ['log', 'array'])"
            >
                <code>{{ config('mailcoach.transactional.mailer') ?? 'null' }}</code>
            </x-mailcoach::checklist-item>
        </table>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Technical Details')">
        @php($issueBody.="\n\n## Technical details\n\n")
        <table>
            @php($issueBody.="**App directory**: " . base_path() . "\n")
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('App directory')"
            >
                <code>{{ base_path() }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**User agent**: " . request()->userAgent() . "\n")
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('User agent')"
            >
                <code>{{ request()->userAgent() }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**PHP version**: " . PHP_VERSION . "\n")
            <x-mailcoach::checklist-item
                neutral
                :label="__mc('PHP')"
            >
                <code>{{ PHP_VERSION }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**" . config('database.default') . " version**: " . $mysqlVersion . "\n")
            <x-mailcoach::checklist-item
                neutral
                :label="ucfirst(config('database.default'))"
            >
                <code>{{ $mysqlVersion }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**Laravel version**: " . app()->version() . "\n")
            <x-mailcoach::checklist-item
                neutral
                label="Laravel"
            >
                <code>{{ app()->version() }}</code>
            </x-mailcoach::checklist-item>
            @php($issueBody.="**Horizon version**: " . $horizonVersion . "\n")
            <x-mailcoach::checklist-item
                neutral
                label="Horizon"
            >
                <code>{{ $horizonVersion }}</code>
            </x-mailcoach::checklist-item>

            @php($issueBody.="**Filament version**: " . $filamentVersion . "\n")
            <x-mailcoach::checklist-item
                neutral
                label="Filament"
            >
                <code>{{ $filamentVersion }}</code>
            </x-mailcoach::checklist-item>

            @php($issueBody.="**laravel-mailcoach version**: " . $versionInfo->getCurrentVersion('laravel-mailcoach') . "\n")
            <x-mailcoach::checklist-item
                neutral
                label="laravel-mailcoach"
            >
                <code>{{ $versionInfo->getCurrentVersion('laravel-mailcoach') }}</code>
                @if(! $versionInfo->isLatest('laravel-mailcoach'))
                    <x-mailcoach::tag neutral size="xs">
                        {{ __mc('Upgrade available') }}
                    </x-mailcoach::tag>
                @endif
            </x-mailcoach::checklist-item>
        </table>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card  :legend="__mc('Having trouble?')">
        <a href="https://github.com/spatie/laravel-mailcoach/issues/new?body={{ urlencode($issueBody) }}" target="_blank">
            <x-mailcoach::button :label="__mc('Create a support issue')"/>
        </a>
    </x-mailcoach::fieldset>
</div>
