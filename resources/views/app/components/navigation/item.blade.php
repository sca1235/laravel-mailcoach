@props([
    'href' => '',
    'active' => false,
])
<li class="w-full" {{ $attributes->except('class') }}>
    <a wire:navigate class="block text-14 rounded-md w-full leading-relaxed {{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'text-blue-dark font-medium' : 'font-normal'  }} {{ $attributes->get('class') }}" href="{{ $href }}">
        {{ $slot }}
    </a>
</li>
