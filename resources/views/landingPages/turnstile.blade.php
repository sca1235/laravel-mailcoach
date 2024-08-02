@extends('mailcoach::landingPages.layouts.landingPage', [
    'title' => __mc('Please confirm that you are not a robot'),
    'noIndex' => true,
])

@section('landing')
    <div class="card text-xl">
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <script>
            window.turnstileCallback = function () {
                document.getElementById('captcha').submit();
            }
        </script>

        <form id="captcha" action="{{ route('mailcoach.subscribe', $emailListUuid) }}" method="POST">
            @foreach ($data as $key => $value)
                @if (is_iterable($value))
                    @foreach ($value as $formKey => $formValue)
                        <input type="hidden" name="{{ $key }}[{{ $formKey }}]" value="{{ $formValue }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <div class="cf-turnstile flex justify-center" data-sitekey="{{ config('mailcoach.turnstile_key') }}" data-callback="turnstileCallback"></div>
            <div class="mx-auto" style="width: 300px;">
                @foreach ($errors as $field => $errorMessages)
                    @foreach ($errorMessages as $errorMessage)
                        <p class="text-sm text-red-500 mt-4">{{ $errorMessage }}</p>
                    @endforeach
                @endforeach

                <div class="flex justify-center w-full">
                    <x-mailcoach::button type="submit" class="mt-4 text-base" :label="__mc('Submit')" />
                </div>
            </div>
        </form>
    </div>
@endsection
