@component('mailcoach::mails.layout.message')
{{ __mc('Hey') }},

{{ __mc('You are almost subscribed to the list **:emailListName**.', ['emailListName'=>$subscriber->emailList->name]) }}

{{ __mc('Prove it is really you by pressing the button below') }}.

@component('mailcoach::mails.layout.button', ['url' => $confirmationUrl, 'attributes' => 'ses:no-track data-pm-no-track clicktracking="off"'])
{{ __mc('Confirm subscription') }}
@endcomponent

@endcomponent
