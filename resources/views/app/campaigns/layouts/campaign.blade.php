<x-mailcoach::layout
    :originTitle="$originTitle ?? $campaign->name"
    :originHref="$originHref ?? null"
    :title="$title"
>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-x-6 h-12">
                <a wire:navigate x-data x-tooltip="'{{ $originTitle ?? __mc('Back to campaigns') }}'" href="{{ $originHref ?? route('mailcoach.campaigns') }}">
                    <svg class="w-5 h-5 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30"><g clip-path="url(#clip0_386_1588)"><path fill="#fff" d="M30 15a15 15 0 1 0-30 0 15 15 0 0 0 30 0Z"/><path fill="#C2C0BC" d="M6.973 15.996a1.4 1.4 0 0 1 0-1.986l6.562-6.569a1.406 1.406 0 0 1 1.986 1.986l-4.16 4.16 10.67.007c.78 0 1.407.627 1.407 1.406 0 .78-.627 1.406-1.407 1.406h-10.67l4.16 4.16a1.406 1.406 0 0 1-1.986 1.986l-6.562-6.556Z"/></g><defs><clipPath id="clip0_386_1588"><path fill="#fff" d="M0 0h30v30H0z"/></clipPath></defs></svg>
                </a>
                <div class="markup-h1 font-title leading-none flex items-center gap-x-3">
                    @if ($originTitle ?? '')
                        <span class="truncate flex items-center gap-x-1">
                            <a href="{{ $originHref ?? route('mailcoach.campaigns') }}" class="opacity-50">
                                {{ $originTitle }}
                            </a>
                            <span class="opacity-50"> / </span>
                            {{ $title }}
                        </span>
                    @else
                        <span class="">{{ $campaign->name }}</span>
                        <x-mailcoach::tag class="hidden sm:block font-sans">
                            {{ $campaign->status->getLabel() }}
                        </x-mailcoach::tag>
                    @endif
                </div>
            </div>
            @if ($campaign->status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Draft && ! Route::is('mailcoach.campaigns.delivery'))
                <a href="{{ route('mailcoach.campaigns.delivery', $campaign) }}" class="hidden sm:flex ml-auto button">
                    <svg class="w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 12"><path fill="#fff" d="M.007 12 14 6 .007 0 0 4.667 10 6 0 7.333.007 12Z"/></svg>
                    <span>{{ __mc('Send campaign') }}</span>
                </a>
            @endif
        </div>
    </x-slot>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            @if ($campaign->isSendingOrSent() || $campaign->isCancelled())
                <x-mailcoach::navigation-group :title="__mc('Performance')" :active="Route::is('mailcoach.campaigns.summary', 'mailcoach.campaigns.opens', 'mailcoach.campaigns.clicks', 'mailcoach.campaigns.outbox')">
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                        {{ __mc('Overview') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $campaign)">
                        {{ __mc('Opens') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)" :active="Route::is('mailcoach.campaigns.clicks') || Route::is('mailcoach.campaigns.link-clicks')">
                        {{ __mc('Clicks') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)">
                        {{ __mc('Unsubscribes') }}
                    </x-mailcoach::navigation-item>

                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)">
                        {{ __mc('Outbox') }}
                    </x-mailcoach::navigation-item>
                </x-mailcoach::navigation-group>
            @endif

            <x-mailcoach::navigation-group
                :href="route('mailcoach.campaigns.content', $campaign)"
                :title="__mc('Content')"
            />

            @if (! $campaign->isSendingOrSent() && ! $campaign->isCancelled())
                <x-mailcoach::navigation-group
                    :href="route('mailcoach.campaigns.settings', $campaign)"
                    :title="__mc('Settings')"
                />
            @endif

            @if (! $campaign->isSendingOrSent() && ! $campaign->isCancelled())
                <x-mailcoach::navigation-group
                    :title="__mc('Send')"
                    :href="route('mailcoach.campaigns.delivery', $campaign)"
                />
            @endif

        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
