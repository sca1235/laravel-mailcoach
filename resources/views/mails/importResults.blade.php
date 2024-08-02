@component('mailcoach::mails.layout.message')
{{ __mc('Good news!') }}

{{ __mc('Your import was processed') }}.

{{ __mc('**:count** :subscriber have been added to the list :emailListName',['count'=>$subscriberImport->imported_subscribers_count,'emailListName'=>$subscriberImport->emailList->name,'subscriber'=> __mc_choice('subscriber|subscribers',$subscriberImport->imported_subscribers_count)]) }}.

@if ($subscriberImport->errorCount())
{{ __mc_choice('There was 1 error.|There were :count errors.', $subscriberImport->errorCount()) }}
@endif

@component('mailcoach::mails.layout.button', ['url' => route('mailcoach.emailLists.import-subscribers', $subscriberImport->emailList)])
{{ __mc('View imports') }}
@endcomponent

@endcomponent
