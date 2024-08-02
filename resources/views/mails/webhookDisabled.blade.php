@component('mailcoach::mails.layout.message')
<h1>{{ __mc('Your webhook has been disabled') }}</h1>

{{ __mc('Your webhook configuration **:name** to **:url** has been disabled.', [
    'name' => $webhookConfiguration->name,
    'url' => $webhookConfiguration->url,
]) }}

{{ __mc('It was attempted **:times** times.', [
    'times' => config('mailcoach.webhooks.maximum_attempts', 5)
]) }}


@if($log = $webhookConfiguration->logs()->first())
{{ __mc('Below you can find the response of the latest log:') }}

<div>
<pre>
<code style="background: rgb(244, 243, 248); width: 500px; max-width: 500px; display: block; white-space: break-spaces;">{{ json_encode($log->response, JSON_PRETTY_PRINT) }}</code>
</pre>
</div>
@endif
@endcomponent
