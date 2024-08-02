<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>
<header class="header">
    @if ($imageUrl = $emailList->websiteHeaderImageUrl())
        <a href="{{ $emailList->websiteUrl() }}">
            <img alt="Header image" src="{{ $imageUrl }}" class="header-image" />
        </a>
    @endif
    <h1>
        <a href="{{ $emailList->websiteUrl() }}">
            {{ $emailList->website_title }}
        </a>
    </h1>
    @if($emailList->website_intro)
        <div class="header-intro">
            {{ app(\Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction::class)->execute($emailList->website_intro) }}
        </div>
    @endif
    @if($emailList->allow_form_subscriptions && $emailList->show_subscription_form_on_website)
        <form
            action="{{ $emailList->incomingFormSubscriptionsUrl() }}"
            method="post"
            accept-charset="utf-8"
            class=""
        >
            @csrf
            {{-- Honeypot field --}}
            @if ($emailList->honeypot_field)
                <div style="position: absolute; left: -9999px">
                    <label for="website-{{ $emailList->honeypot_field }}">Your {{ $emailList->honeypot_field }}</label>
                    <input type="text" id="website-{{ $emailList->honeypot_field }}" name="{{ $emailList->honeypot_field }}" tabindex="-1" autocomplete="nope" />
                </div>
            @endif
            <fieldset>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="{{ __mc('Your email…') }}"
                    aria-label="E-mail"
                    required
                >
                <input
                    type="submit"
                    name="submit"
                    id="submit"
                    value="{{ __mc('Subscribe') }}"
                >
            </fieldset>
            @error('email')
                <p>
                    {{ $message }}
                </p>
            @enderror
        </form>
    @endif
</header>
