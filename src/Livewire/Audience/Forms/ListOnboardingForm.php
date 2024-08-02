<?php

namespace Spatie\Mailcoach\Livewire\Audience\Forms;

use Illuminate\Support\Arr;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ListOnboardingForm extends Form
{
    use UsesMailcoachModels;

    public const CONFIRMATION_MAIL_DEFAULT = 'send_default_confirmation_mail';

    public const CONFIRMATION_MAIL_CUSTOM = 'send_custom_confirmation_mail';

    public EmailList $emailList;

    public bool $dirty = false;

    #[Validate(['boolean'])]
    public ?bool $allow_form_subscriptions = false;

    #[Validate(['nullable'])]
    public ?string $allowed_form_extra_attributes = '';

    #[Validate(['nullable', 'string'])]
    public ?string $honeypot_field = '';

    #[Validate(['boolean'])]
    public ?bool $requires_confirmation;

    #[Validate(['nullable', 'string'])]
    public ?string $redirect_after_subscribed = '';

    #[Validate(['nullable', 'string'])]
    public ?string $redirect_after_already_subscribed = '';

    #[Validate(['nullable', 'string'])]
    public ?string $redirect_after_subscription_pending = '';

    #[Validate(['nullable', 'string'])]
    public ?string $redirect_after_unsubscribed = '';

    public ?int $confirmation_mail_id = null;

    #[Validate(['array'])]
    public ?array $allowed_form_subscription_tags;

    #[Validate(['in:'.self::CONFIRMATION_MAIL_DEFAULT.','.self::CONFIRMATION_MAIL_CUSTOM])]
    public ?string $confirmation_mail = '';

    public function rules(): array
    {
        return [
            'confirmation_mail_id' => [
                'nullable',
                'required_if:form.confirmation_mail,'.self::CONFIRMATION_MAIL_CUSTOM,
                \Illuminate\Validation\Rule::exists(self::getTransactionalMailClass(), 'id'),
            ],
        ];
    }

    public function setEmailList(EmailList $emailList): void
    {
        $this->emailList = $emailList;
        $this->allow_form_subscriptions = $this->emailList->allow_form_subscriptions;
        $this->allowed_form_extra_attributes = $this->emailList->allowed_form_extra_attributes;
        $this->honeypot_field = $this->emailList->honeypot_field;
        $this->requires_confirmation = $this->emailList->requires_confirmation;
        $this->redirect_after_subscribed = $this->emailList->redirect_after_subscribed;
        $this->redirect_after_already_subscribed = $this->emailList->redirect_after_already_subscribed;
        $this->redirect_after_subscription_pending = $this->emailList->redirect_after_subscription_pending;
        $this->redirect_after_unsubscribed = $this->emailList->redirect_after_unsubscribed;
        $this->confirmation_mail_id = $this->emailList->confirmation_mail_id;

        if (! $this->emailList->confirmationMail) {
            $this->confirmation_mail_id = null;
        }

        $this->allowed_form_subscription_tags = $this->emailList->allowedFormSubscriptionTags->pluck('name')->toArray();

        $this->confirmation_mail = $this->emailList->hasCustomizedConfirmationMailFields()
            ? self::CONFIRMATION_MAIL_CUSTOM
            : self::CONFIRMATION_MAIL_DEFAULT;
    }

    public function save(): void
    {
        $this->emailList->fill(Arr::except($this->all(), [
            'emailList',
            'allowed_form_subscription_tags',
            'confirmation_mail',
            'dirty',
        ]));

        if ($this->confirmation_mail === self::CONFIRMATION_MAIL_DEFAULT) {
            $this->emailList->confirmation_mail_id = null;
        }

        $this->emailList->save();

        $this->emailList->allowedFormSubscriptionTags()->sync(self::getTagClass()::whereIn('name', $this->allowed_form_subscription_tags)->pluck('id'));

        $this->dirty = false;
    }

    public function getSubscriptionFormHtml(): string
    {
        $url = $this->emailList->incomingFormSubscriptionsUrl();

        $honeyPot = $this->honeypot_field
            ? <<<html

                <!--
                    This is the honeypot field, this should be invisible to users
                    when filled in, the subscriber won't be created but will still
                    receive a "successfully subscribed" page to fool spam bots.
                -->
                <div style="position: absolute; left: -9999px">
                    <label for="website-{$this->honeypot_field}">Your {$this->honeypot_field}</label>
                    <input type="text" id="website-{$this->honeypot_field}" name="{$this->honeypot_field}" tabindex="-1" autocomplete="nope" />
                </div>
            html
            : '';

        $attributes = '';

        $allowed = explode(',', $this->allowed_form_extra_attributes);
        $allowed = array_map('trim', $allowed);
        $allowed = array_filter($allowed);

        foreach ($allowed as $attribute) {
            $attributes .= <<<html
                <input type="hidden" name="attributes[{$attribute}]" value="your-value" />

            html;
        }

        if ($attributes) {
            $comment = <<<'html'
                <!--
                    You can add extra attributes that you have allowed in the
                    onboarding settings of your email list by adding hidden
                    inputs with the correct attributes[attributeName] name
                -->
            html;

            $attributes = "\n\n".$comment."\n".$attributes;
        }

        return <<<html
        <form
            action="{$url}"
            method="post"
        >
            <input type="email" name="email" placeholder="Your email address" />
            {$honeyPot}
            <!--
                Optional: include any tags. Create them first on the "Tags" section.
                And make sure to allow them in the email list settings
            -->
            <input type="hidden" name="tags" value="tag 1;tag 2" />
            {$attributes}
            <input type="submit" value="Subscribe">
        </form>
        html;
    }
}
