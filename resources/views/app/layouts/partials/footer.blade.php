<div class="mx-auto w-full max-w-layout py-16 flex flex-col gap-y-4 lg:flex-row lg:gap-x-16">
    <div class="lg:ml-auto flex font-medium flex-wrap items-center text-sm text-navy gap-x-9">
        <a class="hover:text-blue-dark" href="https://mailcoach.app/self-hosted/documentation" target="_blank">{{ __mc('Documentation') }}</a>

        @if(Auth::guard(config('mailcoach.guard'))->check())
            <span>
                <a class="inline-block hover:text-blue-dark" href="{{ route('export') }}">
                    {{ __mc('Export') }}
                </a>
                <span class="mx-1">/</span>
                <a class="inline-block hover:text-blue-dark" href="{{ route('import') }}">
                    {{ __mc('Import') }}
                </a>
            </span>
            <a class="inline-block hover:text-blue-dark" href="{{ route('debug') }}">
                {{ __mc('Resolve issues') }}
            </a>
        @endif

        <a class="hover:text-blue-dark" href="https://spatie.be?ref=mailcoach-self-hosted">Made by Spatie</a>
    </div>
</div>
