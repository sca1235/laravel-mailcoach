<?php

namespace Spatie\Mailcoach\Domain\Shared\Traits;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\Click;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Content\Models\Unsubscribe;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Settings\Models\Setting;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Shared\Models\Upload;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

trait UsesMailcoachModels
{
    /** @return class-string<Upload> */
    public static function getUploadClass(): string
    {
        return config('mailcoach.models.upload', Upload::class);
    }

    /** @return class-string<Campaign> */
    public static function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    /** @return class-string<ContentItem> */
    public static function getContentItemClass(): string
    {
        return config('mailcoach.models.content_item', ContentItem::class);
    }

    /** @return string **/
    public static function getContentItemTableName(): string
    {
        $class = self::getContentItemClass();

        /** @var \Illuminate\Database\Eloquent\Model $contentItem */
        $contentItem = new $class;

        return $contentItem->getTable();
    }

    /** @return class-string<Link> */
    public static function getLinkClass(): string
    {
        return config('mailcoach.models.link', Link::class);
    }

    /** @return class-string<Click> */
    public static function getClickClass(): string
    {
        return config('mailcoach.models.click', Click::class);
    }

    /** @return class-string<Open> */
    public static function getOpenClass(): string
    {
        return config('mailcoach.models.open', Open::class);
    }

    /** @return class-string<Unsubscribe> */
    public static function getUnsubscribeClass(): string
    {
        return config('mailcoach.models.unsubscribe', Unsubscribe::class);
    }

    /** @return class-string<EmailList> */
    public static function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    /** @return class-string<Send> */
    public static function getSendClass(): string
    {
        return config('mailcoach.models.send', Send::class);
    }

    /** @return class-string<SendFeedbackItem> */
    public static function getSendFeedbackItemClass(): string
    {
        return config('mailcoach.models.send_feedback_item', SendFeedbackItem::class);
    }

    /** @return class-string<Automation> */
    public static function getAutomationClass(): string
    {
        return config('mailcoach.models.automation', Automation::class);
    }

    /** @return class-string<Action> */
    public static function getAutomationActionClass(): string
    {
        return config('mailcoach.models.automation_action', Action::class);
    }

    /** @return class-string<Trigger> */
    public static function getAutomationTriggerClass(): string
    {
        return config('mailcoach.models.automation_trigger', Trigger::class);
    }

    /** @return class-string<AutomationMail> */
    public static function getAutomationMailClass(): string
    {
        return config('mailcoach.models.automation_mail', AutomationMail::class);
    }

    /** @return class-string<Subscriber> */
    public static function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }

    /** @return class-string<Template> */
    public static function getTemplateClass(): string
    {
        return config('mailcoach.models.template', Template::class);
    }

    /** @return class-string<TransactionalMailLogItem> */
    public static function getTransactionalMailLogItemClass(): string
    {
        return config('mailcoach.models.transactional_mail_log_item', TransactionalMailLogItem::class);
    }

    /** @return class-string<TransactionalMail> */
    public static function getTransactionalMailClass(): string
    {
        return config('mailcoach.models.transactional_mail', TransactionalMail::class);
    }

    /** @return class-string<ActionSubscriber> */
    public static function getActionSubscriberClass(): string
    {
        return config('mailcoach.models.action_subscriber', ActionSubscriber::class);
    }

    /** @return class-string<Tag> */
    public static function getTagClass(): string
    {
        return config('mailcoach.models.tag', Tag::class);
    }

    /** @return class-string<TagSegment> */
    public static function getTagSegmentClass(): string
    {
        return config('mailcoach.models.tag_segment', TagSegment::class);
    }

    /** @return class-string<SubscriberImport> */
    public static function getSubscriberImportClass(): string
    {
        return config('mailcoach.models.subscriber_import', SubscriberImport::class);
    }

    /** @return class-string<SubscriberExport> */
    public static function getSubscriberExportClass(): string
    {
        return config('mailcoach.models.subscriber_export', SubscriberExport::class);
    }

    public static function getEmailListTableName(): string
    {
        $emailListClass = self::getEmailListClass();

        /** @var \Illuminate\Database\Eloquent\Model $emailList */
        $emailList = new $emailListClass;

        return $emailList->getTable();
    }

    public static function getTransactionalMailLogItemTableName(): string
    {
        $logClass = self::getTransactionalMailLogItemClass();

        /** @var \Illuminate\Database\Eloquent\Model $log */
        $log = new $logClass;

        return $log->getTable();
    }

    public static function getTransactionalMailTableName(): string
    {
        $templateClass = self::getTransactionalMailClass();

        /** @var \Illuminate\Database\Eloquent\Model $template */
        $template = new $templateClass;

        return $template->getTable();
    }

    public static function getSubscriberTableName(): string
    {
        $subscriberClass = self::getSubscriberClass();

        /** @var \Illuminate\Database\Eloquent\Model $subscriber */
        $subscriber = new $subscriberClass;

        return $subscriber->getTable();
    }

    public static function getTemplateTableName(): string
    {
        $templateClass = self::getTemplateClass();

        /** @var \Illuminate\Database\Eloquent\Model $template */
        $template = new $templateClass;

        return $template->getTable();
    }

    public static function getTagTableName(): string
    {
        $tagClass = self::getTagClass();

        /** @var \Illuminate\Database\Eloquent\Model $tag */
        $tag = new $tagClass;

        return $tag->getTable();
    }

    public static function getTagSegmentTableName(): string
    {
        $tagSegmentClass = self::getTagSegmentClass();

        /** @var \Illuminate\Database\Eloquent\Model $tagSegment */
        $tagSegment = new $tagSegmentClass;

        return $tagSegment->getTable();
    }

    public static function getCampaignTableName(): string
    {
        $campaignClass = self::getCampaignClass();

        /** @var \Illuminate\Database\Eloquent\Model $campaign */
        $campaign = new $campaignClass;

        return $campaign->getTable();
    }

    public static function getAutomationMailTableName(): string
    {
        $automationMailClass = self::getAutomationMailClass();

        /** @var \Illuminate\Database\Eloquent\Model $automationMail */
        $automationMail = new $automationMailClass;

        return $automationMail->getTable();
    }

    public static function getActionSubscriberTableName(): string
    {
        $actionSubscriberClass = self::getActionSubscriberClass();

        /** @var \Illuminate\Database\Eloquent\Model $actionSubscriber */
        $actionSubscriber = new $actionSubscriberClass;

        return $actionSubscriber->getTable();
    }

    public static function getAutomationTableName(): string
    {
        $automationClass = self::getAutomationClass();

        /** @var \Illuminate\Database\Eloquent\Model $automation */
        $automation = new $automationClass;

        return $automation->getTable();
    }

    public static function getAutomationActionTableName(): string
    {
        $actionClass = self::getAutomationActionClass();

        /** @var \Illuminate\Database\Eloquent\Model $action */
        $action = new $actionClass;

        return $action->getTable();
    }

    public static function getAutomationTriggerTableName(): string
    {
        $className = self::getAutomationTriggerClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getLinkTableName(): string
    {
        $className = self::getLinkClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getClickTableName(): string
    {
        $className = self::getClickClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getOpenTableName(): string
    {
        $className = self::getOpenClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getUnsubscribeTableName(): string
    {
        $className = self::getUnsubscribeClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getSendFeedbackItemTableName(): string
    {
        $className = self::getSendFeedbackItemClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getSendTableName(): string
    {
        $className = self::getSendClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    /** @return class-string<Setting> */
    public static function getSettingClass(): string
    {
        return config('mailcoach.models.setting', Setting::class);
    }

    /** @return class-string<Mailer> */
    public static function getMailerClass(): string
    {
        return config('mailcoach.models.mailer', Mailer::class);
    }

    /** @return class-string<WebhookConfiguration> */
    public static function getWebhookConfigurationClass(): string
    {
        return config('mailcoach.models.webhook_configuration', WebhookConfiguration::class);
    }

    /** @return class-string<WebhookLog> */
    public static function getWebhookLogClass(): string
    {
        return config('mailcoach.models.webhook_log', WebhookLog::class);
    }

    /** @return class-string<Suppression> */
    public static function getSuppressionClass(): string
    {
        return config('mailcoach.models.suppression', Suppression::class);
    }
}
