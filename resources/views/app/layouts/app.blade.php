@php use Spatie\Mailcoach\Mailcoach; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="always">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|inter-tight:600&display=swap" rel="stylesheet" />

    <title>{{ isset($title) ? "{$title} |" : '' }} {{ isset($originTitle) ? "{$originTitle} |" : '' }} Mailcoach</title>

    <link rel="icon" type="image/png" sizes="16x16" href="data:image/svg+xml;base64,{{ base64_encode(view('mailcoach::app.layouts.partials.logoSvg')->render()) }}" />

    <script type="text/javascript">
        window.__ = function (key) {
            return {
                "Are you sure?": "{{ __mc('Are you sure?') }}",
                "Type to add tags": "{{ __mc('Type to add tags') }}",
                "No tags to choose from": "{{ __mc('No tags to choose from') }}",
                "Press to add": "{{ __mc('Press to add') }}",
                "Press to select": "{{ __mc('Press to select') }}",
            }[key];
        };
    </script>

    <style>[x-cloak] { display: none !important; }</style>
    <!-- Filament styles -->
    @filamentStyles
    @livewireStyles
    {!! Mailcoach::styles() !!}
    @include('mailcoach::app.layouts.partials.endHead')
    @stack('endHead')
    @if (config('mailcoach.content_editor') !== \Spatie\Mailcoach\Domain\Editor\Markdown\Editor::class)
        @vite('resources/js/editors/markdown/markdown.js', 'vendor/mailcoach')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    @endif
</head>
<body class="flex flex-col min-h-screen text-navy bg-sand-extra-light font-sans antialiased leading-tight" x-data="{ confirmLabel: @js(__mc('Confirm')), confirmText: '', onConfirm: null, danger: false }">
<script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->

<div class="flex-grow">
    @unless(isset($hideNav) && $hideNav)
        <x-mailcoach::main-navigation/>
    @endunless

    <main class="md:pt-10 relative z-1 w-full px-4.5 sm:px-8">

        <div class="flex-grow md:flex md:items-stretch md:gap-10 @unless(isset($fullWidth) && $fullWidth) max-w-layout mx-auto @endunless">
            @unless(isset($hideNav) && $hideNav)
                <div class="sm:hidden mt-6">
                    @include('mailcoach::app.partials.header')
                </div>
                @isset($nav)
                    <nav class="
                        mb-4 bg-white p-4.5 rounded-xl flex-shrink-0
                        sm:-mt-2 sm:w-[13rem] sm:bg-transparent sm:p-0
                        md:my-0
                    ">
                        {{ $nav }}
                    </nav>
                @endisset
            @endunless

            <section class="flex-grow min-w-0 flex flex-col">
                @unless(isset($hideNav) && $hideNav)
                    <div class="hidden sm:block">
                        @include('mailcoach::app.partials.header')
                    </div>
                @endunless

                <div>
                    {{ $slot }}
                </div>
            </section>
        </div>
    </main>

    <x-mailcoach::modal
        :title="__mc('Confirm')"
        :alignment="\Filament\Support\Enums\Alignment::Center"
        icon="heroicon-s-exclamation-triangle"
        icon-color="danger"
        name="confirm"
        dismissable
    >
        <x-slot:description>
            <span x-text="confirmText"></span>
        </x-slot:description>

        <div class="flex items-center justify-center gap-x-3">
            <x-mailcoach::button-tertiary
                class="w-full"
                x-on:click="$dispatch('close-modal', { id: 'confirm' })"
                :label=" __mc('Cancel')"
            />
            <x-mailcoach::button
                class="w-full"
                type="button"
                x-on:click="onConfirm; $dispatch('close-modal', { id: 'confirm' })"
                x-bind:class="danger ? 'button-red' : ''"
                x-text="confirmLabel"
            />
        </div>
    </x-mailcoach::modal>

    @stack('modals')
</div>

@unless(isset($hideFooter) && $hideFooter)
<footer class="mt-10 px-8">
    @include('mailcoach::app.layouts.partials.footer')
</footer>
@endunless

<aside class="z-50 fixed bottom-4 left-4 w-64">
    @include('mailcoach::app.layouts.partials.startBody')
</aside>

@filamentScripts
@livewire('notifications')
{!! Mailcoach::scripts() !!}
@stack('scripts')
</body>
</html>
