<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>

<div class="flex flex-col gap-8">
    <x-mailcoach::card>
        <h2 class="text-xl font-medium">{{ __mc('Checklist') }}</h2>

        @if ($campaign->isEditable())
            @if (! $campaign->isReady())
                <x-mailcoach::alert type="error">
                    {{ __mc('You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::alert>
            @elseif ($campaign->contentItems->reject->htmlContainsUnsubscribeUrlPlaceHolder()->count() || $campaign->contentItems->filter(fn ($contentItem) => $contentItem->sizeInKb() > 102)->count())
                <x-mailcoach::alert type="warning">
                    {!! __mc('Campaign <strong>:campaign</strong> can be sent, but you might want to check your content.', ['campaign' => $campaign->name]) !!}
                </x-mailcoach::alert>
            @else
                <x-mailcoach::alert type="success">
                    {!! __mc('Campaign <strong>:campaign</strong> is ready to be sent.', ['campaign' => $campaign->name]) !!}
                </x-mailcoach::alert>
            @endif
            @if($campaign->scheduled_at)
                <x-mailcoach::alert type="success">
                {!! __mc('Scheduled for delivery <span class="font-medium">:diff - :scheduledAt</span>.', [
                    'diff' => $campaign->scheduled_at->diffForHumans(),
                    'scheduledAt' => $campaign->scheduled_at->toMailcoachFormat(),
                ]) !!}
                </x-mailcoach::alert>
            @endif

            <section>
                <h3 class="text-lg mb-6">{{ __mc('Settings') }}</h3>
                <table class="w-full">
                    <!-- Email list -->
                    @if ($campaign->emailList)
                        <x-mailcoach::checklist-item
                            :label="__mc('From')"
                            :edit-link="route('mailcoach.emailLists.general-settings', $campaign->emailList)"
                        >
                            <x-slot:value>
                                {{ $fromEmail . ($fromName ? " ({$fromName})" : '') }}
                            </x-slot:value>
                        </x-mailcoach::checklist-item>

                        @if ($replyToEmail)
                            <x-mailcoach::checklist-item
                                :label="__mc('Reply-to')"
                                :edit-link="route('mailcoach.emailLists.general-settings', $campaign->emailList)"
                            >
                                <x-slot:value>
                                    {{ $replyToEmail . ($replyToName ? " ({$replyToName})" : '') }}
                                </x-slot:value>
                            </x-mailcoach::checklist-item>
                        @endif

                        @php($subscribersCount = $campaign->segmentSubscriberCount())
                        <x-mailcoach::checklist-item
                            :label="__mc('To')"
                            :test="$subscribersCount > 0"
                            :edit-link="route('mailcoach.campaigns.settings', $campaign)"
                        >
                            <x-slot:value>
                                @if($subscribersCount)
                                    {{ $campaign->emailList->name }}
                                    @if($campaign->usesSegment())
                                        ({{ $campaign->getSegment()->description() }})
                                    @endif
                                    <x-mailcoach::tag neutral size="xs" class="ml-2">
                                        {{ $subscribersCount ?? '...' }}
                                        @if (!is_null($subscribersCount))
                                            <span class="ml-1 font-normal">
                                                {{ __mc_choice('subscriber|subscribers', $subscribersCount) }}
                                            </span>
                                        @endif
                                    </x-mailcoach::tag>
                                @else
                                    {{ __mc('Selected list has no subscribers') }}
                                @endif
                            </x-slot:value>
                        </x-mailcoach::checklist-item>
                    @else <!-- No email list -->
                        <x-mailcoach::checklist-item
                            :test="false"
                            :label="__mc('Email list')"
                            :value="__mc('No email list')"
                            :edit-link="route('mailcoach.campaigns.settings', $campaign)"
                        />
                    @endif

                    @if ($campaign->emailList)
                        <x-mailcoach::checklist-item
                            warning
                            :test="$campaign->getMailerKey() && $campaign->getMailerKey() !== 'log'"
                            :label="__mc('Mailer')"
                            :value="$campaign->getMailer()?->name ?? $campaign->emailList->campaign_mailer"
                            :edit-link="route('mailcoach.emailLists.general-settings', $campaign->emailList)"
                        />
                    @endif
                </table>
            </section>

            <section class="mt-8">
                <h3 class="text-lg mb-6">
                    {{ __mc('Content') }}
                    @if($campaign->isSplitTested())
                        ({{ $campaign->contentItems->count() }} {{ __mc_choice('variant|variants', $campaign->contentItems->count()) }})
                    @endif
                </h3>

                <div class="grid gap-6 {{ $campaign->isSplitTested() ? 'lg:grid-cols-2' : '' }}">
                    @foreach ($campaign->contentItems as $index => $contentItem)
                        <div class="{{ $campaign->isSplitTested() ? 'border border-snow rounded-md p-6' : '' }}">
                            @include('mailcoach::app.content.checklist', ['model' => $campaign])
                        </div>
                    @endforeach
                </div>
            </section>
        @else <!-- ! $campaign->isEditable() -->
        @endif
    </x-mailcoach::card>

    @if ($campaign->isSplitTested() && $subscribersCount)
        <x-mailcoach::card>
            <header>
                <h2 class="text-xl font-medium mb-3">{{ __mc('Split test settings') }}</h2>
                <p>{{ __mc('Choose how many subscribers will be involved in your tests, and when we decide a winner.') }}</p>
            </header>

            <hr class="border-t border-snow">

            <div x-data="{
                split_size_percentage: @entangle('split_test_split_size_percentage'),
                split_count: {{ $campaign->contentItems->count() }},
                subscriber_count: {{ $campaign->segmentSubscriberCount() }},

                get subscribers_in_test() {
                    return Math.max(this.split_count, Math.floor(this.subscriber_count / 100 * this.split_size_percentage));
                },

                get subscribers_per_split() {
                    return Math.max(1, Math.floor(this.subscribers_in_test / this.split_count));
                }
            }"
             x-cloak>
                <div class="grid lg:grid-cols-2 gap-6 mb-6">
                    <x-mailcoach::card class="border-snow border rounded-md">
                        <h3 class="text-sm font-medium text-center">{{ __mc('Percentage of subscribers involved') }}</h3>
                        <div class="flex items-center gap-6">
                            <input class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" type="range" min="1" step="1" max="50" x-model="split_size_percentage">
                            <input class="w-20 input" type="text" x-bind:value="split_size_percentage + '%'" readonly>
                        </div>
                    </x-mailcoach::card>
                    <x-mailcoach::card class="border-snow border rounded-md">
                        <h3 class="text-sm font-medium text-center">{{ __mc('Hours to wait until deciding a winner') }}</h3>
                        <div class="flex items-center justify-center gap-x-2">
                            <x-mailcoach::text-field
                                :required="true"
                                name="split_length"
                                wire:model.live="split_length"
                                type="number"
                                min="1"
                                input-class="input w-16 pr-2"
                            />
                            @if ($split_length > 1)
                                {{ __mc('hours') }}
                            @else
                                {{ __mc('hour') }}
                            @endif
                        </div>
                    </x-mailcoach::card>
                </div>
                <x-mailcoach::alert type="help">
                    <ul class="text-navy-dark list-disc ml-4">
                        <li><span class="font-medium" x-text="split_size_percentage + '%'"></span> <span class="font-medium">{{ __mc('of your audience') }}</span> <span class="font-medium" x-text="'(' + subscribers_per_split + ' {{ __mc('subscribers') }})'"></span> {{ __mc('will be involved in testing.') }}</li>
                        <li>{{ __mc('Each split receives one variant of your emails.') }}</li>
                        <li>{!! __mc('After <span class="font-medium">:count hours</span>, we will determine the winning variant.', ['count' => $split_length]) !!}</li>
                        <li>{{ __mc('The winning variant will then be sent to the') }} <span class="font-medium">{{ __mc('remaining') }} <span class="font-semibold" x-text="(Math.max(0, subscriber_count - (subscribers_per_split * split_count))) + ' {{ __mc('subscribers') }}'"></span></span></li>
                    </ul>
                </x-mailcoach::alert>
            </div>

            <div>
                <x-mailcoach::button wire:click.prevent="saveSplitTestSettings">{{ __mc('Save settings') }}</x-mailcoach::button>
            </div>
        </x-mailcoach::card>
    @endif

    <x-mailcoach::card>
        <header>
            <h2 class="text-xl font-medium">{{ __mc('Send campaign') }}</h2>
        </header>

        @if (count($validateErrors = $campaign->validateRequirements()))
            @foreach ($validateErrors as $error)
                <x-mailcoach::alert type="error" full>{!! $error !!}</x-mailcoach::alert>
            @endforeach
        @endif
        <div>
            @if ($campaign->isReady())
                <div class="w-full flex flex-col" x-init="schedule = '{{ $campaign->scheduled_at || $errors->first('scheduled_at') ? 'future' : 'now' }}'"
                     x-data="{ schedule: '' }" x-cloak>
                    @if($campaign->scheduled_at)
                        <x-mailcoach::alert type="success" class="w-full" full>
                            <p class="mb-3">
                                {{ __mc('This campaign is scheduled to be sent at') }}

                                <strong>{{ $campaign->scheduled_at->toMailcoachFormat() }}</strong>.
                            </p>
                        </x-mailcoach::alert>
                        <x-mailcoach::button :label="__mc('Unschedule')" class="mt-4 mr-auto" type="submit" wire:click.prevent="unschedule">
                            <x-slot:icon>
                                <x-heroicon-s-stop class="w-4" />
                            </x-slot:icon>
                        </x-mailcoach::button>
                    @else
                        <div class="grid gap-3 items-start mb-6">
                            <x-mailcoach::radio-field
                                name="schedule"
                                option-value="now"
                                :label="__mc('Send immediately')"
                                x-model="schedule"
                            />
                            <x-mailcoach::radio-field
                                name="schedule"
                                option-value="future"
                                :label="__mc('Schedule for delivery in the future')"
                                x-model="schedule"
                            />
                        </div>

                        <form
                            method="POST"
                            wire:submit="schedule"
                            x-show="schedule === 'future'"
                        >
                            @csrf

                            <x-mailcoach::date-time-field
                                name="scheduled_at"
                                :value="$scheduled_at_date"
                                required
                            />
                            <p class="mt-2 text-xs text-gray-400">
                                {{ __mc('All times in :timezone', ['timezone' => config('mailcoach.timezone') ?? config('app.timezone')]) }}
                            </p>

                            <x-mailcoach::button type="submit" :label="__mc('Schedule delivery')" class="mt-6 button">
                                <x-slot:icon>
                                    <svg class="h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 12"><path fill="#fff" d="M.007 12 14 6 .007 0 0 4.667 10 6 0 7.333.007 12Z"/></svg>
                                </x-slot:icon>
                            </x-mailcoach::button>

                        </form>
                    @endif

                    <div x-show="schedule === 'now'">
                        <x-mailcoach::button
                            x-on:click="$dispatch('open-modal', { id: 'send-campaign' })"
                            :label="__mc('Send now')"
                        >
                            <x-slot:icon>
                                <svg class="h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 12"><path fill="#fff" d="M.007 12 14 6 .007 0 0 4.667 10 6 0 7.333.007 12Z"/></svg>
                            </x-slot:icon>
                        </x-mailcoach::button>
                    </div>
                    <x-mailcoach::modal name="send-campaign" :dismissable="true">
                        <div class="grid gap-8 p-6">
                            <p class="text-lg">
                                {{ __mc('Are you sure you want to send this campaign to') }}
                                <strong class="font-semibold">
                                    @if ($subscribersCount = $campaign->segmentSubscriberCount())
                                        {{ number_format($subscribersCount) }}
                                        {{ $subscribersCount === 1 ? __mc('subscriber') : __mc('subscribers') }}
                                    @endif
                                </strong>?
                            </p>

                            <x-mailcoach::button
                                x-on:click.prevent="$dispatch('send-campaign')"
                                class="button button-red"
                                :label="__mc('Yes, send now!')"
                            />
                        </div>
                    </x-mailcoach::modal>
                </div>
            @else
                <x-mailcoach::alert type="error">
                    {{ __mc('You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::alert>
            @endif
        </div>
    </x-mailcoach::card>
</div>
