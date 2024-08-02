<?php

namespace Spatie\Mailcoach\Livewire\Audience\Forms;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\ValidationRules\Rules\Delimited;

class ListSettingsForm extends Form
{
    public bool $dirty = false;

    #[Validate('required')]
    public string $name;

    #[Validate(['required', 'email:strict'])]
    public ?string $default_from_email;

    #[Validate(['nullable'])]
    public ?string $default_from_name;

    #[Validate([new Delimited('email')])]
    public ?string $default_reply_to_email;

    #[Validate(['nullable'])]
    public ?string $default_reply_to_name;

    #[Validate(['boolean'])]
    public ?bool $campaigns_feed_enabled = false;

    #[Validate(['boolean'])]
    public ?bool $report_campaign_sent = false;

    #[Validate(['boolean'])]
    public ?bool $report_campaign_summary = false;

    #[Validate(['boolean'])]
    public ?bool $report_email_list_summary = false;

    #[Validate([
        new Delimited('email'),
        'required_if:report_email_list_summary,true',
        'required_if:report_campaign_sent,true',
        'required_if:report_campaign_summary,true',
    ])]
    public ?string $report_recipients;

    public ?string $campaign_mailer;

    public ?string $automation_mailer;

    public ?string $transactional_mailer;

    public EmailList $emailList;

    public function rules(): array
    {
        return [
            'campaign_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
            'automation_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
            'transactional_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
        ];
    }

    public function setEmailList(EmailList $emailList)
    {
        $this->emailList = $emailList;

        $this->name = $emailList->name;
        $this->default_from_email = $emailList->default_from_email;
        $this->default_from_name = $emailList->default_from_name;
        $this->default_reply_to_email = $emailList->default_reply_to_email;
        $this->default_reply_to_name = $emailList->default_reply_to_name;
        $this->campaigns_feed_enabled = $emailList->campaigns_feed_enabled;
        $this->report_campaign_sent = $emailList->report_campaign_sent;
        $this->report_campaign_summary = $emailList->report_campaign_summary;
        $this->report_email_list_summary = $emailList->report_email_list_summary;
        $this->report_recipients = $emailList->report_recipients;
        $this->campaign_mailer = $emailList->campaign_mailer;
        $this->automation_mailer = $emailList->automation_mailer;
        $this->transactional_mailer = $emailList->transactional_mailer;
    }

    public function update(): void
    {
        $this->emailList->update(Arr::except($this->all(), ['emailList', 'dirty']));
    }
}
