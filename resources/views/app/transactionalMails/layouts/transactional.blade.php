<x-mailcoach::layout
    :originTitle="$originTitle ?? $transactionalMail->subject"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    {{ $slot }}
</x-mailcoach::layout>
