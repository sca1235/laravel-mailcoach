<div class="card-grid">
    @if ($transactionalMail->fake)
        <x-mailcoach::alert type="info">
            {{ __mc('This email was programmatically faked and was not actually delivered') }}
        </x-mailcoach::alert>
    @endif
    @php($openCount = $transactionalMail->contentItem->opens->count())
    @php($clickCount = $transactionalMail->contentItem->clicks->count())
    <x-mailcoach::card class="px-0">
        <div class="grid sm:grid-cols-2 sm:divide-x divide-y sm:divide-y-0 divide-snow">
            <x-mailcoach::statistic
                :stat="number_format($openCount)"
            >
                <x-slot:label>
                    <div>
                        <p>{{ __mc_choice('Open|Opens', $openCount) }}</p>
                        @if($openCount)
                            <p class="text-xs mt-4">{{ __mc('First opened at') }} {{ $transactionalMail->contentItem->opens->first()->created_at->toMailcoachFormat() }}</p>
                        @endif
                    </div>
                </x-slot:label>
            </x-mailcoach::statistic>
            <x-mailcoach::statistic
                :stat="number_format($clickCount)"
                :label="__mc_choice('Click|Clicks', $clickCount)"
            />
        </div>
    </x-mailcoach::card>
    @if ($clickCount)
        @php($clicksPerUrl = $transactionalMail->clicksPerUrl())
        <h2 class="text-xl font-medium">{{ __mc('Clicks') }}</h2>
        <x-mailcoach::card class="flex flex-wrap sm:flex-nowrap items-center justify-around sm:divide-x divide-snow">
            @foreach ($clicksPerUrl as $clickGroup)
                <x-mailcoach::statistic
                    class="w-full"
                    :stat="$clickGroup['count']"
                >
                    <x-slot:label>
                        <div>
                            <p><a href="{{ $clickGroup['url'] }}" target="_blank">{{ $clickGroup['url'] }}</a></p>
                            <p class="text-xs mt-4">{{ __mc('First clicked at') }} {{ $clickGroup['first_clicked_at'] }}</p>
                        </div>
                    </x-slot:label>
                </x-mailcoach::statistic>
            @endforeach
        </x-mailcoach::card>
    @endif
    <h2 class="text-xl font-medium">{{ __mc('Details') }}</h2>
    <x-mailcoach::card>
        <table class="w-full">
            <x-mailcoach::address-definition :label="__mc('From')" :addresses="$transactionalMail->from"/>
            <x-mailcoach::address-definition :label="__mc('To')" :addresses="$transactionalMail->to"/>
            <x-mailcoach::address-definition :label="__mc('Cc')" :addresses="$transactionalMail->cc"/>
            <x-mailcoach::address-definition :label="__mc('Bcc')" :addresses="$transactionalMail->bcc"/>
            @if ($transactionalMail->getMedia('attachments')->count())
                <x-mailcoach::checklist-item
                    neutral
                    :label="__mc('Attachments')"
                >
                    <ul>
                        <?php /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $attachment */ ?>
                        @foreach($transactionalMail->getMedia('attachments') as $attachment)
                            <li>
                                <button wire:click.prevent="downloadAttachment({{ $attachment->id }})" class="underline" type="button">{{ $attachment->file_name }}</button>
                            </li>
                        @endforeach
                    </ul>
                </x-mailcoach::checklist-item>
            @elseif (collect($transactionalMail->attachments)->count() > 0)
                <x-mailcoach::checklist-item
                    neutral
                    :label="__mc('Attachments')"
                >
                    <ul>
                        @foreach(collect($transactionalMail->attachments) as $attachment)
                            <li>
                                {{ $attachment }}
                            </li>
                        @endforeach
                    </ul>
                </x-mailcoach::checklist-item>
            @endif
        </table>
    </x-mailcoach::card>

    <h2 class="text-xl font-medium">{{ __mc('Content') }}</h2>
    @include('mailcoach::app.content.view', ['model' => $transactionalMail])

    @if (! $transactionalMail->fake)
        <h2 class="text-xl font-medium">{{ __mc('Resend') }}</h2>
        <x-mailcoach::card>
            @if($transactionalMail->contentItem->opens->count())
                <x-mailcoach::alert type="warning">{{ __mc('This mail has already been opened, are you sure you want to resend it?') }}</x-mailcoach::alert>
            @else
                <x-mailcoach::alert type="info">{{ __mc('This mail hasn\'t been opened yet.') }}</x-mailcoach::alert>
            @endif
            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Resend')" class="button" wire:click.prevent="resend" />
            </x-mailcoach::form-buttons>
        </x-mailcoach::card>
    @endif
</div>
