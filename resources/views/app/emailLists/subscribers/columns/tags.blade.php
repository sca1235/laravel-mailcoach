@php($subscriber = $getRecord())
@php($tags = $subscriber
                ->tags
                ->where('type', \Spatie\Mailcoach\Domain\Audience\Enums\TagType::Default)
)
<div class="fi-ta-text-item inline-flex pb-4 items-center gap-1.5 flex-wrap">
    @foreach($tags->take(3) as $tag)
        <a href="{{ route('mailcoach.emailLists.tags.edit', [$subscriber->emailList, $tag]) }}">
            <x-mailcoach::tag neutral size="2xs">
                {{ $tag->name }}
            </x-mailcoach::tag>
        </a>
    @endforeach
    @if ($tags->count() - 3 > 0)
        <span class="py-1 px-2 text-2xs">{{ __mc('+:count more', ['count' => $tags->count() - 3]) }}</span>
    @endif
</div>
