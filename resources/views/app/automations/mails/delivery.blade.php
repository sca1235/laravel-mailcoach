<?php /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $mail */ ?>
<x-mailcoach::card>
    <h2 class="text-xl font-medium">{{ __mc('Checklist') }}</h2>

    @if ($mail->contentItem->sendsCount() > 0)
        <x-mailcoach::alert type="success" sync>
            {{ __mc('Automation mail') }}
            <a class="font-medium" target="_blank" href="{{ $mail->webviewUrl() }}">{{ $mail->name }}</a>

            {{ __mc('has been sent to :sendsCount :subscriber', [
                'sendsCount' => $mail->contentItem->sendsCount(),
                'subscriber' => __mc_choice('subscriber|subscribers', $mail->contentItem->sendsCount())
            ]) }}.
        </x-mailcoach::alert>
    @endif

    @if($mail->isReady())
        @if (! $mail->contentItem->htmlContainsUnsubscribeUrlPlaceHolder() || $mail->contentItem->sizeInKb() > 102)
            <x-mailcoach::alert type="warning">
                <p>{!! __mc('Automation mail <strong>:automationMail</strong> can be sent, but you might want to check your content.', ['automationMail' => $mail->name]) !!}</p>
            </x-mailcoach::alert>
        @else
            @if (! $mail->contentItem->hasValidHtml())
                <x-mailcoach::alert type="error">
                    {!! __mc('Your campaign HTML is invalid according to <a href=":url" target="_blank">the guidelines</a>, please make sure it displays correctly in the email clients you need.', ['url' => 'https://www.caniemail.com/']) !!}
                </x-mailcoach::alert>
            @else
                <x-mailcoach::alert type="success">
                    {!! __mc('Automation mail <strong>:automationMail</strong> is ready to be sent.', ['automationMail' => $mail->name]) !!}
                </x-mailcoach::alert>
            @endif
        @endif
    @else
        <x-mailcoach::alert type="error">
            {{ __mc('You need to check some settings before you can deliver this mail.') }}
        </x-mailcoach::alert>
    @endif

    <section>
        <h3 class="text-lg mb-6">{{ __mc('Content') }}</h3>
        @include('mailcoach::app.content.checklist', ['model' => $mail, 'contentItem' => $mail->contentItem])
    </section>
</x-mailcoach::card>
