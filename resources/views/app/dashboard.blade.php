<?php
$usesVapor = \Composer\InstalledVersions::isInstalled('laravel/vapor-core');
$hasQueueConnection = config('queue.connections.mailcoach-redis') && ! empty(config('queue.connections.mailcoach-redis'));
$horizonStatus = app(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::class);
?>
<div class="relative pb-48 min-h-[50vh]" wire:init="loadData">
    <h1 class="text-3.5xl font-semibold font-title mb-8">
        {{ __mc('Hi') }}@if (array_key_exists('name', $attributes = Auth::guard(config('mailcoach.guard'))->user()->attributesToArray())), {{ str($attributes['name'])->ucfirst() }}@endif
    </h1>
    <div class="grid md:grid-cols-12 gap-6">
        @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
            <x-mailcoach::tile warning cols="3" icon="heroicon-s-credit-card" :title="__mc('License')" link="https://spatie.be/products/mailcoach" :link-label="__mc('Renew license')">
                {{ __mc('Your Mailcoach license has expired.') }} <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardTiles')

        @if (!$usesVapor && ! $horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE))
            <x-mailcoach::tile title="Horizon" danger cols="3" icon="heroicon-s-circle-stack" link="https://mailcoach.app/docs" target="_blank">
                {!! __mc('Horizon is not active on your server') !!}
            </x-mailcoach::tile>
        @endif

        @if (\Spatie\Mailcoach\MailcoachServiceProvider::getMailerClass()::count() === 0)
            <x-mailcoach::tile :title="__mc('Mailer missing')" danger cols="3" icon="heroicon-s-envelope" link="{{ route('mailers') }}" :link-label="__mc('Add a mailer')">
                {!! __mc('You need to add at least 1 mailer.') !!}
            </x-mailcoach::tile>
        @endif

        @if (!$usesVapor && ! $hasQueueConnection)
            <x-mailcoach::tile :title="__mc('Queue error')" danger cols="3" icon="heroicon-s-circle-stack" link="https://mailcoach.app/docs" target="_blank">
                {!! __mc('No queue connection found. Configure a queue connection with the <code>mailcoach-redis</code> key.</p>') !!}
            </x-mailcoach::tile>
        @endif

        <x-mailcoach::tile :title="__mc('Subscribers')" cols="3" icon="heroicon-s-user-group" link="{{ route('mailcoach.emailLists') }}" :link-label="__mc('View email lists')">
            <div class="flex flex-col">
                <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark flex items-center gap-x-3">
                    {{ !is_null($recentSubscribers) ? $this->abbreviateNumber($recentSubscribers) : '...' }}
                    @if ($previousPeriodSubscribers)
                        @php($growth = round(($recentSubscribers - $previousPeriodSubscribers) / $previousPeriodSubscribers * 100))
                        <x-mailcoach::tag class="{{ $growth > 0 ? '!bg-green-extra-light !text-green-dark' : '!bg-sky-extra-light text-navy' }}">
                            {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                        </x-mailcoach::tag>
                    @endif
                </span>
                <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Last 30 days') }}</span>
            </div>
        </x-mailcoach::tile>

        <x-mailcoach::tile class="" cols="3" icon="heroicon-s-envelope-open" link="{{ route('mailcoach.campaigns') }}" :link-label="__mc('View all campaigns')">
            <x-slot:title>
                {{ __mc('Campaigns') }}
            </x-slot:title>
            <div class="flex justify-between">
                @php($draftCount = $this->getCampaignClass()::draft()->count())
                <a href="{{ route('mailcoach.campaigns') }}?status=draft" class="flex flex-col pointer-events-auto hover:text-blue group">
                    <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $this->abbreviateNumber($draftCount) }}</span>
                    <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc_choice('Draft|Drafts', $draftCount) }}</span>
                </a>

                @php($scheduledCount = $this->getCampaignClass()::scheduled()->count())
                <a href="{{ route('mailcoach.campaigns') }}?status=scheduled" class="flex flex-col pointer-events-auto hover:text-blue group">
                    <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $this->abbreviateNumber($scheduledCount) }}</span>
                    <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Scheduled') }}</span>
                </a>

                @php($sentCount = $this->getCampaignClass()::sent()->count())
                <a href="{{ route('mailcoach.campaigns') }}?status=sent" class="flex flex-col pointer-events-auto hover:text-blue group">
                    <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $this->abbreviateNumber($sentCount) }}</span>
                    <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Sent') }}</span>
                </a>
            </div>
        </x-mailcoach::tile>

        <?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $latestCampaign */ ?>
        @if ($latestCampaign)
            <x-mailcoach::tile :title="$latestCampaign->name" icon="heroicon-s-envelope-open" class="" cols="4" link="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}" :link-label="__mc('View campaign')">
                <div class="flex justify-between">
                    <a href="{{ route('mailcoach.campaigns.opens', $latestCampaign) }}" class="flex flex-col pointer-events-auto hover:text-blue group">
                        <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $latestCampaign->uniqueOpenCount() ? $this->abbreviateNumber($latestCampaign->uniqueOpenCount()) : '–' }}</span>
                        <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Opens') }}</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.clicks', $latestCampaign) }}" class="flex flex-col pointer-events-auto hover:text-blue group">
                        <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $latestCampaign->uniqueClickCount() ? $this->abbreviateNumber($latestCampaign->uniqueClickCount()) : '–' }}</span>
                        <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Clicks') }}</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.unsubscribes', $latestCampaign) }}" class="flex flex-col pointer-events-auto hover:text-blue group">
                        <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $this->abbreviateNumber($latestCampaign->unsubscribeCount()) }}</span>
                        <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Unsubs') }}</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.outbox', $latestCampaign) }}" class="flex flex-col pointer-events-auto hover:text-blue group">
                        <span class="text-3.5xl font-semibold font-title mb-1 group-hover:text-blue-dark">{{ $this->abbreviateNumber($latestCampaign->bounceCount()) }}</span>
                        <span class="font-medium text-sm text-navy-bleak-light group-hover:text-blue-dark">{{ __mc('Bounces') }}</span>
                    </a>
                </div>
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardGraph')

        <div class="col-span-12">
            <livewire:mailcoach::dashboard-chart lazy />
        </div>

        @include('mailcoach::app.layouts.partials.afterDashboardGraph')

    </div>
    <svg class="w-36 absolute bottom-0 left-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 142 134"><path fill="#E1DCCC" fill-rule="evenodd" d="M62 30V8h-4v22H36v4h22v22h4V34h22v-4H62ZM127 87V76h-2v11h-11v2h11v11h2V89h11v-2h-11ZM17 117v-11h-2v11H4v2h11v11h2v-11h11v-2H17Z" clip-rule="evenodd"/></svg>
</div>
