<?php /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $template */ ?>
<div class="card-grid">
    <x-mailcoach::card>
        <div class="grid grid-cols-1 divide-x divide-snow">
            <x-mailcoach::statistic
                :stat="number_format($template->sentToNumberOfSubscribers())"
                :label="__mc('Recipients')"
            />
        </div>
    </x-mailcoach::card>

    @if ($template->openCount())
        <h2 class="text-xl font-medium">{{ __mc('Opens') }}</h2>
        <x-mailcoach::card class="px-0">
            <div class="grid grid-cols-3 divide-x divide-snow">
                <x-mailcoach::statistic
                    :stat="$template->openCount()"
                    :label="__mc('Opens')"
                />

                <x-mailcoach::statistic
                    :stat="$template->uniqueOpenCount()"
                    :label="__mc('Unique opens')"
                />

                <x-mailcoach::statistic
                    :stat="$template->openRate() / 100"
                    :label="__mc('Open rate')"
                    suffix="%"
                />
            </div>
        </x-mailcoach::card>
    @endif

    @if ($template->clickCount())
        <h2 class="text-xl font-medium">{{ __mc('Clicks') }}</h2>
        <x-mailcoach::card class="px-0">
            <div class="grid grid-cols-3 divide-x divide-snow">
                <x-mailcoach::statistic
                    :stat="$template->clickCount()"
                    :label="__mc('Clicks')"
                />

                <x-mailcoach::statistic
                    :stat="$template->uniqueClickCount()"
                    :label="__mc('Unique clicks')"
                />

                <x-mailcoach::statistic
                    :stat="$template->clickRate() / 100"
                    :label="__mc('Click rate')"
                    suffix="%"
                />
            </div>
        </x-mailcoach::card>
    @endif
</div>
