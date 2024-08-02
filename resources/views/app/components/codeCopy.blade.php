@props([
    'code' => '',
    'buttonPosition' => 'bottom',
    'buttonClass' => '',
    'codeClass' => '',
    'lang' => null,
])
<div style="background-color: #fff" {{ $attributes->except(['code', 'lang', 'buttonClass'])->merge([
    'class' => 'relative markup markup-code rounded-md text-navy text-sm pr-6'
]) }}>
    @if ($buttonPosition === 'top')
        <div x-data class="{{ $buttonClass }} absolute text-white pr-6 z-20">
            <button type="button" class="text-sm" @click.prevent="$clipboard(@js($code));" x-tooltip.on.click="'Copied!'">
                <svg class="h-4 fill-current text-navy" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 16"><path d="M6.5 0h3.878a1.5 1.5 0 0 1 1.06.44l2.121 2.123A1.5 1.5 0 0 1 14 3.622V10.5a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 5 10.5v-9A1.5 1.5 0 0 1 6.5 0Zm-5 4H4v2H2v8h6v-1h2v1.5A1.5 1.5 0 0 1 8.5 16h-7A1.5 1.5 0 0 1 0 14.5v-9A1.5 1.5 0 0 1 1.5 4Z"/></svg>
            </button>
        </div>
    @endif

    <x-mailcoach::code
        :lang="$lang"
        :code="$code"
        :code-class="$codeClass"
    />

    @if ($buttonPosition === 'bottom')
    <div x-data>
        <button type="button" class="text-sm link-dimmed" x-tooltip.on.click="'Copied!'" @click.prevent="$clipboard(@js($code));">
            <svg class="h-4 fill-current text-navy" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 16"><path d="M6.5 0h3.878a1.5 1.5 0 0 1 1.06.44l2.121 2.123A1.5 1.5 0 0 1 14 3.622V10.5a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 5 10.5v-9A1.5 1.5 0 0 1 6.5 0Zm-5 4H4v2H2v8h6v-1h2v1.5A1.5 1.5 0 0 1 8.5 16h-7A1.5 1.5 0 0 1 0 14.5v-9A1.5 1.5 0 0 1 1.5 4Z"/></svg>
        </button>
    </div>
    @endif
</div>
