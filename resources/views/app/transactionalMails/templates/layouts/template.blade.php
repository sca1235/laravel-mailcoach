<x-mailcoach::layout
    :originTitle="$originTitle ?? $template->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            <x-mailcoach::navigation-group
                :title="__mc('Performance')"
                :href="route('mailcoach.transactionalMails.templates.summary', $template)"
            />
            <x-mailcoach::navigation-group
                :title="__mc('Content')"
                :href="route('mailcoach.transactionalMails.templates.edit', $template)"
            />
            <x-mailcoach::navigation-group
                :title="__mc('Settings')"
                :href="route('mailcoach.transactionalMails.templates.settings', $template)"
            />
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
