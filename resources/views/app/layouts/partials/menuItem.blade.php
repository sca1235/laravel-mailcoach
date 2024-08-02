<?php /** @var \Spatie\Mailcoach\Domain\Settings\Support\MenuItem $item */ ?>
@if ($item->isForm)
    <form class="block text-sm font-medium" method="post" action="{{ $item->url }}">
        {{ csrf_field() }}
        <button type="submit" class="flex items-center gap-x-2 hover:text-blue-dark group">
            @if ($item->icon)
                <x-icon :name="$item->icon" class="w-5 text-navy-bleak-extra-light group-hover:text-blue-dark" />
            @endif
            {{ $item->label }}
        </button>
    </form>
@elseif ($item->isDivider)
    <div class="py-2 -mx-5">
        <div class="h-px bg-sand-light"></div>
    </div>
@else
    <a class="block text-sm" href="{{ $item->url }}" @if($item->isExternal) target="_blank" @elseif(str_starts_with($item->url, config('app.url'))) wire:navigate @endif>
        <span class="flex items-center gap-x-2 hover:text-blue-dark group">
            @if ($item->icon)
                <x-icon :name="$item->icon" class="w-5 text-navy-bleak-extra-light group-hover:text-blue-dark" />
            @endif
            <span href="{{ $item->url }}">{{ $item->label }}</span>
        </span>
    </a>
@endif
