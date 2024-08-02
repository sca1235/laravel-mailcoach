<?php /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $mail */ ?>
<x-mailcoach::card class="px-0">
    <div class="grid grid-cols-3 divide-x divide-snow">
        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $mail)"
            :stat="number_format($mail->sentToNumberOfSubscribers())"
            :label="__mc('Recipients')"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.unsubscribes', $mail)"
            :stat="$mail->unsubscribeRate() / 100"
            :label="__mc('Unsubscribe Rate')"
            suffix="%"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $mail) . '?filter[type][value]=bounced&tableFilters[type][value]=bounced'"
            :stat="$mail->bounceRate() / 100"
            :label="__mc('Bounce Rate')"
            suffix="%"
        />
    </div>
</x-mailcoach::card>
@if ($mail->openCount())
    <h2 class="text-xl font-medium">{{ __mc('Opens') }}</h2>
    <x-mailcoach::card class="px-0">
        <div class="grid grid-cols-3 divide-x divide-snow">
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $mail)"
                :stat="number_format($mail->openCount())"
                :label="__mc('Opens')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $mail)"
                :stat="number_format($mail->uniqueOpenCount())"
                :label="__mc('Unique opens')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $mail)"
                :stat="$mail->openRate() / 100"
                :label="__mc('Open rate')"
                suffix="%"
            />
        </div>
    </x-mailcoach::card>
@endif

@if ($mail->clickCount())
    <h2 class="text-xl font-medium">{{ __mc('Clicks') }}</h2>
    <x-mailcoach::card class="px-0">
        <div class="grid grid-cols-4 divide-x divide-snow">
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $mail)"
                :stat="number_format($mail->clickCount())"
                :label="__mc('Clicks')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $mail)"
                :stat="number_format($mail->uniqueClickCount())"
                :label="__mc('Unique clicks')"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $mail)"
                :stat="$mail->clickRate() / 100"
                :label="__mc('Click rate')"
                suffix="%"
            />

            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $mail)"
                :stat="number_format($mail->uniqueClickCount() / $mail->uniqueOpenCount() * 100)"
                :label="__mc('Click through rate')"
                suffix="%"
            />
        </div>
    </x-mailcoach::card>
@endif
