@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Unsubscribed')])

@section('landing')
    <div class="card text-xl">
        <p class="mt-4">
            {!! __mc('Are you sure you want to unsubscribe from list <strong class="font-semibold">:emailListName</strong>?', ['emailListName' => $subscriber->emailList->name]) !!}
        </p>

        <div class="mt-4">
            <form method="POST">
                @csrf
                <button class="button shadow" id="confirmationButton" type="submit">{{__mc('Unsubscribe') }}</button>
            </form>
        </div>
    </div>
@endsection
