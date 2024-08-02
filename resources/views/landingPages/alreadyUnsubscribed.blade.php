@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Already unsubscribed')])

@section('landing')
    <div class="card text-xl">
        <p>
            {!! __mc('You were already unsubscribed from the list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $emailList->name]) !!}
        </p>
    </div>
@endsection
