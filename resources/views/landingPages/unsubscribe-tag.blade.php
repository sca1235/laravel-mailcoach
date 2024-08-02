@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Unsubscribed')])

@section('landing')
    <div class="card text-xl">
        <p class="mt-4">
            {!! __mc('Are you sure you want to unsubscribe from list <strong class="font-semibold">:emailListName</strong>\'s tag :tag?', ['emailListName' => $subscriber->emailList->name, 'tag' => $tag]) !!}
        </p>

        <div class="mt-4">
            <form method="POST">
                @csrf
                <button class="button bg-red-400 shadow" id="confirmationButton" type="submit">{{__mc('Unsubscribe') }}</button>
            </form>
        </div>
    </div>
@endsection
