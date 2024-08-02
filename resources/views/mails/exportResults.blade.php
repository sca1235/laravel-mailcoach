@component('mailcoach::mails.layout.message')
{{ __mc('Good news!') }}

{{ __mc('Your export was completed') }}.

{{ __mc('**:count** :subscriber have been exported from the list :emailListName',[
    'count'=> $subscriberExport->exported_subscribers_count,
    'emailListName'=> $subscriberExport->emailList->name,
    'subscriber'=> __mc_choice('subscriber|subscribers', $subscriberExport->exported_subscribers_count)
]) }}.

@if ($subscriberExport->errors)
{{ __mc_choice('There was 1 error.|There were :count errors.', count($subscriberExport->errors ?? [])) }}
@endif

@component('mailcoach::mails.layout.button', ['url' => route('mailcoach.emailLists.subscriber-exports', $subscriberExport->emailList)])
{{ __mc('View exports') }}
@endcomponent

@endcomponent
