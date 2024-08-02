<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
@component('mailcoach::mails.layout.message')
{{ __mc('Good job!') }}

{{ __mc('Campaign **:campaignName** was sent to **:numberOfSubscribers** subscribers (list :emailListName)',[
    'campaignName' => $campaign->name,
    'numberOfSubscribers' => ($campaign->sentToNumberOfSubscribers() ?? 0 ),
    'emailListName' => $campaign->emailList->name]
) }}.

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
{{ __mc('View summary') }}
@endcomponent

@component('mailcoach::mails.layout.subcopy')
[{{ __mc('Edit notification settings') }}]({{ $settingsUrl }})
@endcomponent

@endcomponent
