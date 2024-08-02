@props([
    'title' => null,
    'href' => '',
    'active' => false,
])
@php
$isActive = ($active || \Illuminate\Support\Str::startsWith($href, request()->url()));
@endphp
<div {{ $attributes }}>
    @if($title)
        <li class="{{ $isActive ? 'text-blue-dark' : ''  }}">
            @if($href)
            <a wire:navigate href="{{ $href }}">
                {{ $title ?? '' }}
            </a>
            @else
            <span>
                {{ $title ?? '' }}
            </span>
            @endif
        </li>
    @endif
    @if ((string) $slot)
        <ul class="
            mt-1 flex gap-x-3 overflow-x-auto gap-y-2
            md:mt-3 md:items-start md:flex-col">
            {{ $slot }}
        </ul>
    @endif
</div>
