<?php /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $mail */ ?>
<div class="card-grid">
    @if ($mail->sentToNumberOfSubscribers())
        <x-mailcoach::alert type="success"  full>
            <div>
                {{ __mc('AutomationMail') }}
                <strong>{{ $mail->name }}</strong>
                {{ __mc('was delivered to') }}
                <strong>{{ number_format($mail->sentToNumberOfSubscribers() - ($failedSendsCount ?? 0)) }} {{ __mc_choice('subscriber|subscribers', $mail->sentToNumberOfSubscribers()) }}</strong>
            </div>
        </x-mailcoach::alert>
    @else
        <x-mailcoach::alert type="warning"  full>
            <div>
                {{ __mc('Automation email') }}
                <strong>{{ $mail->name }}</strong>
                {{ __mc('has not been sent yet.') }}
            </div>
        </x-mailcoach::alert>
    @endif

    @if($failedSendsCount)
        <x-mailcoach::alert type="error"  full>
            <div>
                {{ __mc('Delivery failed for') }}
                <strong>{{ $failedSendsCount }}</strong> {{ __mc_choice('subscriber|subscribers', $failedSendsCount) }}
                .
                <a class="underline"
                   href="{{ route('mailcoach.automations.mails.outbox', $mail) . '?filter[type]=failed' }}">{{ __mc('Check the outbox') }}</a>.
            </div>
        </x-mailcoach::alert>
    @endif

    @include('mailcoach::app.automations.mails.partials.statistics')
</div>
