<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

/** @mixin EmailList */
class EmailListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'active_subscribers_count' => $this->totalSubscriptionsCount(),
            'campaigns_feed_enabled' => (bool) $this->campaigns_feed_enabled,

            'default_from_email' => $this->default_from_email,
            'default_from_name' => $this->default_from_name,

            'default_reply_to_email' => $this->default_reply_to_email,
            'default_reply_to_name' => $this->default_reply_to_name,

            'allow_form_subscriptions' => (bool) $this->allow_form_subscriptions,
            'honeypot_field' => $this->honeypot_field,

            'redirect_after_subscribed' => $this->redirect_after_subscribed,
            'redirect_after_already_subscribed' => $this->redirect_after_already_subscribed,
            'redirect_after_subscription_pending' => $this->redirect_after_subscription_pending,
            'redirect_after_unsubscribed' => $this->redirect_after_unsubscribed,

            'requires_confirmation' => (bool) $this->requires_confirmation,
            'confirmation_mailable_class' => $this->confirmation_mailable_class,

            'campaign_mailer' => $this->campaign_mailer,
            'automation_mailer' => $this->automation_mailer,
            'transactional_mailer' => $this->transactional_mailer,

            'report_recipients' => $this->report_recipients,
            'report_campaign_sent' => $this->report_campaign_sent,
            'report_campaign_summary' => $this->report_campaign_summary,
            'report_email_list_summary' => $this->report_email_list_summary,

            'email_list_summary_sent_at' => $this->email_list_summary_sent_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
