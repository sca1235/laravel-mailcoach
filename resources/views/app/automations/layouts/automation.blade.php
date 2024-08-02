<x-mailcoach::layout
        :originTitle="$originTitle ?? $automation->name"
        :originHref="$originHref ?? null"
        :title="$title ?? null"
        :hide-footer="$hideFooter ?? false"
        :full-width="$fullWidth ?? false"
>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            <x-mailcoach::navigation-group :href="route('mailcoach.automations.settings', $automation)">
                <x-slot:title>
                    {{ __mc('Settings') }}
                </x-slot:title>
            </x-mailcoach::navigation-group>
            <x-mailcoach::navigation-group :href="route('mailcoach.automations.actions', $automation)">
                <x-slot:title>
                    {{ __mc('Actions') }}
                </x-slot:title>
            </x-mailcoach::navigation-group>
            <x-mailcoach::navigation-group :href="route('mailcoach.automations.subscribers', $automation)">
                <x-slot:title>
                    {{ __mc('Subscribers') }}
                </x-slot:title>
            </x-mailcoach::navigation-group>
            <x-mailcoach::navigation-group
                    x-data="{ running: {{ $automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::Started ? 'true' : 'false' }} }"
                    @automation-started.window="running = true"
                    @automation-paused.window="running = false"
                    :href="route('mailcoach.automations.run', $automation)"

            >
                <x-slot:title>
                    <span class="flex items-center gap-2">
                        <span>{{ __mc('Run')}} </span>
                        <x-heroicon-s-arrow-path x-show="running" class="w-5 opacity-75 animate-spin" />
                    </span>
                </x-slot:title>
            </x-mailcoach::navigation-group>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
