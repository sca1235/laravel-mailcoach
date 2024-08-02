@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Confirm subscription')])

@section('landing')
    <div class="card text-xl">
        <p>
            {{ __mc('Hey, is that really you?') }}
        </p>
        <p class="mt-4">
            {{ __mc("We've sent you an email to confirm your subscription.") }}
        </p>

        <x-mailcoach::sniper-link
            :email="$subscriber->email"
            :sender="$subscriber->emailList->default_from_email ?? config('mail.from.address')"
            class="mt-4 text-sm"
        />
    </div>
@endsection
