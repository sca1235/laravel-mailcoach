@extends('mailcoach::landingPages.layouts.landingPage', [
    'title' => __mc('This endpoint requires a POST request'),
    'noIndex' => true,
])

@section('landing')
    <div class="card text-xl">
        <p>
            {{ __mc('Whoops!') }}
        </p>
        <p class="mt-4">
            {{ __mc('This endpoint requires a POST request. Make sure your subscribe form is doing a POST and not a GET request.') }}
        </p>
    </div>
@endsection
