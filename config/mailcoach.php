<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\ConditionCollection;
use Spatie\Mailcoach\Mailcoach;

return [
    'campaigns' => [
        /*
         * The default mailer used by Mailcoach for sending campaigns.
         */
        'mailer' => null,

        /*
         * Replacers are classes that can make replacements in the html of a campaign.
         *
         * You can use a replacer to create placeholders.
         */
        'replacers' => Campaign::defaultReplacers()->merge([
            // CustomCampaignReplacer::class
        ])->toArray(),

        /*
         * Here you can specify which jobs should run on which queues.
         * Use an empty string to use the default queue.
         */
        'perform_on_queue' => [
            'send_campaign_job' => 'send-campaign',
            'send_mail_job' => 'send-mail',
            'send_test_mail_job' => 'mailcoach',
            'process_feedback_job' => 'mailcoach-feedback',
            'import_subscribers_job' => 'mailcoach',
            'export_subscribers_job' => 'mailcoach',
        ],

        /*
         * The job that will send a campaign could take a long time when your list contains a lot of subscribers.
         * Here you can define the maximum run time of the job. If the job hasn't fully sent your campaign, it
         * will redispatch itself.
         */
        'send_campaign_maximum_job_runtime_in_seconds' => 60 * 10,

        /*
         * You can customize some of the behavior of this package by using our own custom action.
         * Your custom action should always extend the one of the default ones.
         */
        'actions' => Campaign::defaultActions()->merge([
            // 'prepare_email_html' => \App\Mailcoach\Campaign\Actions\CustomPrepareEmailHtmlAction::class,
        ])->toArray(),

        /*
         * Adapt these settings if you prefer other default settings for newly created campaigns
         */
        'default_settings' => [
            'utm_tags' => true,
        ],

        /**
         * Here you can configure which fields of the campaigns you want to search in
         * from the Campaigns section in the view. The value is an array of fields.
         * For relations fields, you can use the dot notation (e.g. 'emailList.name').
         */
        'search_fields' => ['name'],
    ],

    'automation' => [
        /*
         * The default mailer used by Mailcoach for automation mails.
         */
        'mailer' => null,

        /*
         * The job that will send automation mails could take a long time when your list contains a lot of subscribers.
         * Here you can define the maximum run time of the job. If the job hasn't fully sent your automation mails, it
         * will redispatch itself.
         */
        'send_automation_mails_maximum_job_runtime_in_seconds' => 60 * 10,

        /*
         * You can customize some of the behavior of this package by using our own custom action.
         * Your custom action should always extend the one of the default ones.
         */
        'actions' => Automation::defaultActions()->merge([
            // 'prepare_email_html' => \App\Mailcoach\Automation\Actions\CustomPrepareEmailHtmlAction::class,
        ])->toArray(),

        'replacers' => Automation::defaultReplacers()->merge([
            // Add any custom replacers here
        ])->toArray(),

        'flows' => [
            /**
             * The available actions in the automation flows. You can add custom
             * actions to this array, make sure they extend
             * \Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction
             */
            'actions' => AutomationAction::defaultActions()->merge([
                // \App\Mailcoach\Automation\AutomationActions\CustomAction::class,
            ])->toArray(),

            /**
             * The available triggers in the automation settings. You can add
             * custom triggers to this array, make sure they extend
             * \Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger
             */
            'triggers' => AutomationTrigger::defaultTriggers()->merge([
                // \App\Mailcoach\Automation\AutomationTriggers\CustomTrigger::class,
            ])->toArray(),

            /**
             * Custom conditions for the ConditionAction, these have to implement the
             * \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition
             * interface.
             */
            'conditions' => [],
        ],

        'perform_on_queue' => [
            'dispatch_pending_automation_mails_job' => 'send-campaign',
            'run_automation_action_job' => 'send-campaign',
            'run_action_for_subscriber_job' => 'mailcoach',
            'run_automation_for_subscriber_job' => 'mailcoach',
            'send_automation_mail_to_subscriber_job' => 'send-automation-mail',
            'send_automation_mail_job' => 'send-mail',
            'send_test_mail_job' => 'mailcoach',
        ],

        /*
         * Adapt these settings if you prefer other default settings for newly created campaigns
         */
        'default_settings' => [
            'utm_tags' => true,
        ],
    ],

    'audience' => [
        /*
         * You can customize some of the behavior of this package by using our own custom action.
         * Your custom action should always extend the one of the default ones.
         */
        'actions' => Subscriber::defaultActions()->merge([
            // 'confirm_subscriber' => \App\Mailcoach\Audience\Actions\Subscribers\CustomConfirmSubscriberAction::class,
        ])->toArray(),

        /*
         * This disk will be used to store files regarding importing subscribers.
         */
        'import_subscribers_disk' => 'local',

        /*
         * This disk will be used to store files regarding exporting subscribers.
         */
        'export_subscribers_disk' => 'local',

        /**
         * Here you can configure which condition builder
         * conditions Mailcoach supports.
         */
        'condition_builder_conditions' => ConditionCollection::defaultConditions()->merge([
            // Add extra conditions here
        ])->toArray(),
    ],

    'transactional' => [
        /*
         * The default mailer used by Mailcoach for transactional mails.
         */
        'mailer' => null,

        /*
         * Replacers are classes that can make replacements in the body of transactional mails.
         *
         * You can use replacers to create placeholders.
         */
        'replacers' => [
            'subject' => \Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\SubjectReplacer::class,
        ],

        'actions' => [
            'send_test' => \Spatie\Mailcoach\Domain\TransactionalMail\Actions\SendTestForTransactionalMailTemplateAction::class,
            'render_template' => \Spatie\Mailcoach\Domain\TransactionalMail\Actions\RenderTemplateAction::class,
        ],

        /**
         * Here you can configure which fields of the transactional mails you want to search in
         * from the Transactional Log section in the view. The value is an array of fields.
         * For relations fields, you can use the dot notation.
         */
        'search_fields' => ['contentItem.subject'],
    ],

    /*
     * Here you can specify which jobs should run on which queues.
     * Use an empty string to use the default queue.
     */
    'perform_on_queue' => [
        'schedule' => 'mailcoach-schedule',
        'calculate_statistics_job' => 'mailcoach',
        'send_webhooks' => 'mailcoach',
    ],

    'actions' => Mailcoach::defaultActions()->merge([
        // 'send_webhook' => App\Mailcoach\Actions\SendWebhookAction::class,
    ])->toArray(),

    /*
     * Here you can configure which content editor Mailcoach uses.
     * By default this is a text editor that highlights HTML.
     */
    'content_editor' => \Spatie\Mailcoach\Domain\Editor\Markdown\Editor::class,

    /*
     * Here you can configure which template editor Mailcoach uses.
     * By default this is a text editor that highlights HTML.
     */
    'template_editor' => \Spatie\Mailcoach\Domain\Editor\Codemirror\Editor::class,

    /*
     * This disk will be used to store files regarding importing.
     */
    'import_disk' => 'local',

    /*
     * This disk will be used to store files regarding exporting.
     */
    'export_disk' => 'local',

    /*
     * This disk will be used to store assets for the public archive
     * of an email list. You should make sure that this disk is
     * publicly reachable.
     */
    'website_disk' => 'public',

    /*
     * We will put all mailcoach files in this directory
     * on the disk.
     */
    'website_disk_directory' => 'mailcoach-files',

    /**
     * The prefix in the URL we use for email list websites
     */
    'website_prefix' => 'archive',

    /*
     * This disk will be used to store files temporarily for
     * unzipping & reading. Make sure this is on a local
     * filesystem.
     */
    'tmp_disk' => 'local',

    /*
     * The mailer used by Mailcoach for password resets and summary emails.
     * Mailcoach will use the default Laravel mailer if this is not set.
     */
    'mailer' => null,

    /*
     * The timezone to use with Mailcoach, by default the timezone in
     * config/app.php will be used.
     */
    'timezone' => null,

    /*
     * The date format used on all screens of the UI
     */
    'date_format' => 'Y-m-d H:i',

    /*
     * Here you can specify on which connection Mailcoach's jobs will be dispatched.
     * Leave empty to use the app default's env('QUEUE_CONNECTION')
     */
    'queue_connection' => '',

    /*
     * Here you can specify the database connection for Mailcoach's models
     * Leave empty to use the app default's env('DB_CONNECTION')
     */
    'database_connection' => '',

    /*
     * Homepage will redirect to this route.
     */
    'redirect_home' => 'mailcoach.dashboard',

    /**
     * You can enable Cloudflare Turnstile spam protection
     * by providing a site key & secret here. Make sure
     * you have configured the domain correctly.
     */
    'turnstile_key' => '',
    'turnstile_secret' => '',

    /*
     *  These middleware will be assigned to every Mailcoach routes, giving you the chance
     *  to add your own middleware to this stack or override any of the existing middleware.
     */
    'middleware' => [
        'web' => [
            'web',
            'auth',
            Spatie\Mailcoach\Http\App\Middleware\Authorize::class,
            Spatie\Mailcoach\Http\App\Middleware\BootstrapMailcoach::class,
            Spatie\Mailcoach\Http\App\Middleware\BootstrapNavigation::class,
        ],
        'api' => [
            'api',
            'auth:sanctum',
        ],
    ],

    'uploads' => [
        /*
         * The disk on which to store uploaded images from the editor. Choose
         * one or more of the disks you've configured in config/filesystems.php.
         */
        'disk_name' => env('MEDIA_DISK', 'public'),

        /*
         * The media collection name to use when storing uploaded images from the editor.
         * You probably don't need to change this,
         * unless you're already using spatie/laravel-medialibrary in your project.
         */
        'collection_name' => env('MEDIA_COLLECTION', 'default'),

        /**
         * The max width that will be set for the uploaded conversion
         */
        'max_width' => 1500,

        /**
         * The max height that will be set for the uploaded conversion
         */
        'max_height' => 1500,
    ],

    /*
     * The models you want Mailcoach to use. When overriding
     * a model your custom model should always extend the
     * model you're replacing.
     */
    'models' => Mailcoach::defaultModels()->merge([
        // 'campaign' => App\Mailcoach\Models\CustomCampaign::class,
    ])->toArray(),

    /**
     * You can override any Livewire component used by Mailcoach by
     * referencing the FQCN of the component as the key and adding
     * your replacement class as the value. Your component must
     * be a Livewire component and extend \Livewire\Component
     */
    'livewire' => [
        // \Spatie\Mailcoach\Livewire\Campaigns\CreateCampaignComponent::class => \App\Livewire\CustomCreateCampaignComponent::class
    ],

    /**
     * The available editors inside Mailcoach UI, the key is the displayed name in the UI
     * the class must be a class that extends and implements
     * \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver
     */
    'editors' => Mailcoach::defaultEditors()->merge([
        // \App\Mailcoach\Editors\MyCustomEditorConfigurationDriver::class,
    ])->toArray(),

    'editor' => [
        'editor_js' => [
            /** Here you can configure the supported block */
            'renderers' => [
                'header' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\HeaderRenderer::class,
                'list' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\ListRenderer::class,
                'paragraph' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\ParagraphRenderer::class,
                'quote' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\QuoteRenderer::class,
                'raw' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\RawRenderer::class,
                'table' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\TableRenderer::class,
                'delimiter' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\DelimiterRenderer::class,
                'image' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\ImageRenderer::class,
                'code' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\CodeRenderer::class,
                'button' => \Spatie\Mailcoach\Domain\Editor\EditorJs\Renderer\ButtonRenderer::class,
            ],
        ],
    ],

    'webhooks' => [
        /**
         * The amount of times a webhook call should be retried before giving up.
         */
        'maximum_attempts' => 5,

        /**
         * The email addresses which will be notified when a webhook is auto disabled.
         */
        'notified_emails' => null,
    ],
];
