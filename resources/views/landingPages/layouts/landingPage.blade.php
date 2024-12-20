<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="preconnect" href="https://fonts.gstatic.com">
        @if (isset($noIndex) && $noIndex === true)
        <meta name="robots" content="noindex">
        @endif

        <title>{{ isset($title) ? "{$title} | Mailcoach" : 'Mailcoach' }}</title>

        @filamentStyles
        @livewireStyles
        {!! \Spatie\Mailcoach\Mailcoach::styles() !!}
    </head>
    <body class="text-gray-800 bg-indigo-900/5">

    <div id="app">
        <div class="min-h-screen flex flex-col">
            <div class="flex-grow flex items-center justify-center mx-12 my-4">
                <div class="w-full {{ $size ?? 'max-w-md' }}">
                    <div class="flex justify-center -mb-4 z-10">
                        <a href="/" class="group w-16 h-16 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full shadow-lg">
                            <span class="w-10 h-10 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </a>
                    </div>
                    @yield('landing')
                </div>
            </div>

            <footer class="mx-auto w-full max-w-layout p-6 flex justify-center text-sm text-gray-500">
                <a href="https://mailcoach.app">
                    Powered by Mailcoach
                </a>
            </footer>
        </div>
    </div>
    @filamentScripts
    @livewireScripts
</body>
</html>
