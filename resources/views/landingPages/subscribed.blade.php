@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Subscribed')])

@section('landing')
    <div class="card text-xl">
        <p>
            {{ __mc('Happy to have you!') }}
        </p>
        <p class="mt-4">
            @if(isset($subscriber) && $subscriber->emailList)
                {!! __mc('You are now subscribed to the list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
            @elseif (isset($subscriber))
                {!! __mc('You are now subscribed.') !!}
            @else
                {!! __mc('You are now subscribed to the list <strong class="font-semibold">our dummy mailing list</strong>.') !!}
            @endif
        </p>
    </div>
@endsection
