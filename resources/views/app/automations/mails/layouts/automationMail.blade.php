<x-mailcoach::layout
    :originTitle="$originTitle ?? $mail->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-x-6">
                <a wire:navigate x-data x-tooltip="'{{ __mc('Back to automations') }}'" href="{{ route('mailcoach.automations') }}">
                    <svg class="w-5 h-5 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30"><g clip-path="url(#clip0_386_1588)"><path fill="#fff" d="M30 15a15 15 0 1 0-30 0 15 15 0 0 0 30 0Z"/><path fill="#C2C0BC" d="M6.973 15.996a1.4 1.4 0 0 1 0-1.986l6.562-6.569a1.406 1.406 0 0 1 1.986 1.986l-4.16 4.16 10.67.007c.78 0 1.407.627 1.407 1.406 0 .78-.627 1.406-1.407 1.406h-10.67l4.16 4.16a1.406 1.406 0 0 1-1.986 1.986l-6.562-6.556Z"/></g><defs><clipPath id="clip0_386_1588"><path fill="#fff" d="M0 0h30v30H0z"/></clipPath></defs></svg>
                </a>
                <span class="markup-h1 font-title leading-none">{{ $mail->name }}</span>
            </div>
        </div>
    </x-slot>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            <x-mailcoach::navigation-group :active="Route::is('mailcoach.automations.mails.summary', 'mailcoach.automations.mails.opens', 'mailcoach.automations.mails.clicks', 'mailcoach.automations.mails.unsubscribes', 'mailcoach.automations.mails.outbox')" :href="route('mailcoach.automations.mails.summary', $mail)" :title="__mc('Performance')">
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.summary', $mail)">
                    {{ __mc('Overview') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.opens', $mail)">
                    {{ __mc('Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.clicks', $mail)">
                    {{ __mc('Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.unsubscribes', $mail)">
                    {{ __mc('Unsubscribes') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.outbox', $mail)">
                    {{ __mc('Outbox') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            <x-mailcoach::navigation-group
                :title="__mc('Settings')"
                :href="route('mailcoach.automations.mails.settings', $mail)"
            />
            <x-mailcoach::navigation-group
                :title="__mc('Content')"
                :href="route('mailcoach.automations.mails.content', $mail)"
            />
            <x-mailcoach::navigation-group
                :title="__mc('Checklist')"
                :href="route('mailcoach.automations.mails.delivery', $mail)"
            />
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
