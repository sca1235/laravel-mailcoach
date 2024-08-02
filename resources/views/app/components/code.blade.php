@props([
    'code' => null,
    'codeClass' => '',
    'lang' => null,
    'clickToCopy' => false,
])
@php($code ??= (string) $slot)
<div
    {{ $attributes->except(['code', 'lang', 'buttonClass', 'codeClass'])->merge([
        'class' => 'relative markup markup-code w-full max-w-full overflow-x-auto'
    ]) }}
    @if ($clickToCopy)
        x-data
        x-tooltip.on.click="'{{ __mc('Copied!') }}'"
        x-on:click="$clipboard(@js($code))"
    @endif
>
    @if ($lang)
        {!! app(\Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction::class)->execute(<<<markdown
        ```{$lang}
        {$code}
        ```
        markdown) !!}
    @else
        <pre class="max-w-full code overflow-x-auto relative z-10 {{ $codeClass }}">{{ $code }}</pre>
    @endif
</div>
