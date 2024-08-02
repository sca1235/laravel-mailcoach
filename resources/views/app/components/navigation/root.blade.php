<div class="md:sticky md:top-14 {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <ul class="flex flex-col gap-3 md:gap-6 md:pt-3 text-lg font-medium">
        {{ $slot }}
    </ul>
</div>
