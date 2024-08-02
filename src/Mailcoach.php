<?php

namespace Spatie\Mailcoach;

use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Domain\Settings\Support\MenuItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Front\Controllers\MailcoachAssets;

class Mailcoach
{
    use UsesMailcoachModels;

    protected static array $editorScripts = [];

    protected static array $editorStyles = [];

    /** @var MenuItem[] */
    public static array $mainMenuItems = [];

    /** @var array{before: MenuItem[], after: MenuItem[]} */
    public static array $userMenuItems = [
        'before' => [],
        'after' => [],
    ];

    /** @var array{before: MenuItem[], after: MenuItem[]} */
    public static array $settingsMenuItems = [
        'before' => [],
        'after' => [],
    ];

    public static function flushState(): void
    {
        static::$editorScripts = [];
        static::$editorStyles = [];
        static::$mainMenuItems = [];
        static::$userMenuItems = [
            'before' => [],
            'after' => [],
        ];
        static::$settingsMenuItems = [
            'before' => [],
            'after' => [],
        ];
    }

    public static function getDatabaseDriver(?string $connection = null): string
    {
        $key = \is_null($connection) ? Config::get('database.default') : $connection;

        return strtolower(Config::get('database.connections.'.$key.'.driver'));
    }

    public static function isMysqlDatabase(?string $connection = null): bool
    {
        return self::getDatabaseDriver($connection) === 'mysql';
    }

    public static function isPostgresqlDatabase(?string $connection = null): bool
    {
        return self::getDatabaseDriver($connection) === 'pgsql';
    }

    public static function styles(): string
    {
        $version = InstalledVersions::getPrettyVersion('spatie/laravel-mailcoach');

        // Default to dynamic `app.css` (served by a Laravel route).
        $fullAssetPath = action([MailcoachAssets::class, 'style']);

        // Use static assets if they have been published
        if (file_exists(public_path('vendor/mailcoach/manifest.json'))) {
            $manifest = json_decode(file_get_contents(public_path('vendor/mailcoach/manifest.json')), true);
            $fullAssetPath = asset("/vendor/mailcoach/{$manifest['resources/css/app.css']['file']}");
        }

        $styles = [];

        if (is_file(__DIR__.'/../resources/hot')) {
            $url = rtrim(file_get_contents(__DIR__.'/../resources/hot'));

            $fullAssetPath = "{$url}/resources/css/app.css";
            $styles[] = sprintf('<script type="module" src="%s"></script>', "{$url}/@vite/client");
        }

        $styles[] = "<link rel=\"stylesheet\" href=\"{$fullAssetPath}?v={$version}\" type=\"text/css\">";

        foreach (self::availableEditorStyles() as $editor => $editorStyles) {
            if (! in_array($editor, [config('mailcoach.content_editor'), config('mailcoach.template_editor')])) {
                continue;
            }

            foreach ($editorStyles as $style) {
                $styles[] = "<link rel=\"stylesheet\" href=\"{$style}?v={$version}\">";
            }
        }

        return implode("\n", $styles);
    }

    public static function scripts(): string
    {
        $version = InstalledVersions::getPrettyVersion('spatie/laravel-mailcoach');

        // Default to dynamic `app.js` (served by a Laravel route).
        $fullAssetPath = action([MailcoachAssets::class, 'script']);

        // Use static assets if they have been published
        if (file_exists(public_path('vendor/mailcoach/manifest.json'))) {
            $manifest = json_decode(file_get_contents(public_path('vendor/mailcoach/manifest.json')), true);
            $fullAssetPath = asset("/vendor/mailcoach/{$manifest['resources/js/app.js']['file']}");
        }

        $scripts = [];

        foreach (self::availableEditorScripts() as $editor => $editorScripts) {
            if (! in_array($editor, [config('mailcoach.content_editor'), config('mailcoach.template_editor')])) {
                continue;
            }

            foreach ($editorScripts as $script) {
                if (str_contains($script, 'unlayer')) {
                    $scripts[] = "<script src=\"{$script}?v={$version}\" data-navigate-track></script>";

                    continue;
                }

                $scripts[] = "<script type=\"module\" src=\"{$script}?v={$version}\" data-navigate-track></script>";
            }
        }

        if (is_file(__DIR__.'/../resources/hot')) {
            $url = rtrim(file_get_contents(__DIR__.'/../resources/hot'));

            $scripts[] = sprintf('<script type="module" src="%s" defer data-navigate-track></script>',
                "{$url}/resources/js/app.js");
            $scripts[] = sprintf('<script type="module" src="%s" defer data-navigate-track></script>',
                "{$url}/@vite/client");
        } else {
            $scripts[] = <<<HTML
                <script type="module" src="{$fullAssetPath}?v={$version}" data-navigate-once defer data-navigate-track></script>
            HTML;
        }

        return implode("\n", $scripts);
    }

    public static function availableEditorScripts(): array
    {
        return static::$editorScripts;
    }

    public static function editorScript(string $editor, string $url): static
    {
        static::$editorScripts[$editor][] = $url;

        return new static;
    }

    public static function availableEditorStyles(): array
    {
        return static::$editorStyles;
    }

    public static function editorStyle(string $editor, string $url): static
    {
        static::$editorStyles[$editor][] = $url;

        return new static;
    }

    public static function defaultCampaignMailer(): ?string
    {
        return config('mailcoach.campaigns.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');
    }

    public static function defaultAutomationMailer(): ?string
    {
        return config('mailcoach.automation.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');
    }

    public static function defaultTransactionalMailer(): ?string
    {
        return config('mailcoach.transactional.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');
    }

    public static function defaultSystemMailer(): ?string
    {
        return config('mailcoach.mailer') ?? config('mail.default');
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    public static function getCampaignActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.campaigns.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    public static function getSharedActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    public static function getAutomationActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.automation.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    public static function getAudienceActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.audience.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    public static function getTransactionalActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.transactional.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $actionClass
     * @return T
     */
    protected static function getActionClass(?string $configuredClass, string $actionName, string $actionClass): object
    {
        if (is_null($configuredClass)) {
            $configuredClass = $actionClass;
        }

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass, $actionClass);
        }

        return resolve($configuredClass);
    }

    public static function getLivewireClass(string $defaultClass): string
    {
        $configuredClass = config("mailcoach.livewire.{$defaultClass}", $defaultClass);

        if (! is_subclass_of($configuredClass, Component::class)) {
            throw InvalidConfig::invalidLivewireComponent($defaultClass, $configuredClass);
        }

        return $configuredClass;
    }

    public static function getDatabaseConnection(): ?string
    {
        return config('mailcoach.database_connection') ?? config('database.default');
    }

    public static function getQueueConnection(): ?string
    {
        return config('mailcoach.queue_connection') ?? config('queue.default');
    }

    public static function addMainMenuItems(MenuItem ...$items): void
    {
        foreach ($items as $item) {
            self::$mainMenuItems[] = $item;
        }
    }

    public static function addUserMenuItemsBefore(MenuItem ...$items): void
    {
        foreach ($items as $item) {
            self::$userMenuItems['before'][] = $item;
        }
    }

    public static function addUserMenuItemsAfter(MenuItem ...$items): void
    {
        foreach ($items as $item) {
            self::$userMenuItems['after'][] = $item;
        }
    }

    public static function addSettingsMenuItemsBefore(MenuItem ...$items): void
    {
        foreach ($items as $item) {
            self::$settingsMenuItems['before'][] = $item;
        }
    }

    public static function addSettingsMenuItemsAfter(MenuItem ...$items): void
    {
        foreach ($items as $item) {
            self::$settingsMenuItems['after'][] = $item;
        }
    }

    public static function defaultModels(): Collection
    {
        return collect([
            'campaign' => \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class,
            'content_item' => \Spatie\Mailcoach\Domain\Content\Models\ContentItem::class,
            'link' => \Spatie\Mailcoach\Domain\Content\Models\Link::class,
            'click' => \Spatie\Mailcoach\Domain\Content\Models\Click::class,
            'open' => \Spatie\Mailcoach\Domain\Content\Models\Open::class,
            'unsubscribe' => \Spatie\Mailcoach\Domain\Content\Models\Unsubscribe::class,
            'email_list' => \Spatie\Mailcoach\Domain\Audience\Models\EmailList::class,
            'send' => \Spatie\Mailcoach\Domain\Shared\Models\Send::class,
            'send_feedback_item' => \Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem::class,
            'subscriber' => \Spatie\Mailcoach\Domain\Audience\Models\Subscriber::class,
            'subscriber_import' => \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport::class,
            'subscriber_export' => \Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport::class,
            'tag' => \Spatie\Mailcoach\Domain\Audience\Models\Tag::class,
            'tag_segment' => \Spatie\Mailcoach\Domain\Audience\Models\TagSegment::class,
            'template' => \Spatie\Mailcoach\Domain\Template\Models\Template::class,
            'transactional_mail_log_item' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem::class,
            'transactional_mail' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail::class,
            'automation' => \Spatie\Mailcoach\Domain\Automation\Models\Automation::class,
            'automation_action' => \Spatie\Mailcoach\Domain\Automation\Models\Action::class,
            'automation_trigger' => \Spatie\Mailcoach\Domain\Automation\Models\Trigger::class,
            'automation_mail' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::class,
            'action_subscriber' => \Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber::class,
            'upload' => \Spatie\Mailcoach\Domain\Shared\Models\Upload::class,
            'setting' => \Spatie\Mailcoach\Domain\Settings\Models\Setting::class,
            'mailer' => \Spatie\Mailcoach\Domain\Settings\Models\Mailer::class,
            'webhook_configuration' => \Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration::class,
            'webhook_log' => \Spatie\Mailcoach\Domain\Settings\Models\WebhookLog::class,
            'suppression' => \Spatie\Mailcoach\Domain\Audience\Models\Suppression::class,
        ]);
    }

    public static function defaultEditors(): Collection
    {
        return collect([
            Domain\Editor\EditorJs\EditorJsEditorConfigurationDriver::class,
            Domain\Editor\Markdown\MarkdownEditorConfigurationDriver::class,
            Domain\Editor\Codemirror\CodeMirrorEditorConfigurationDriver::class,
            Domain\Editor\Unlayer\UnlayerEditorConfigurationDriver::class,
        ]);
    }

    public static function defaultActions(): Collection
    {
        return collect([
            'calculate_statistics' => \Spatie\Mailcoach\Domain\Content\Actions\CalculateStatisticsAction::class,
            'send_webhook' => \Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction::class,
            'resend_webhook' => \Spatie\Mailcoach\Domain\Settings\Actions\ResendWebhookCallAction::class,
            'initialize_mjml' => \Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction::class,
            'render_twig' => \Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction::class,
            'is_email_on_suppression_list' => \Spatie\Mailcoach\Domain\Shared\Actions\EnsureEmailsNotOnSuppressionListAction::class,
            'personalize_text' => \Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction::class,
            'convert_html_to_text' => \Spatie\Mailcoach\Domain\Content\Actions\ConvertHtmlToTextAction::class,
            'prepare_email_html' => \Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction::class,
            'prepare_webview_html' => \Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction::class,
            'send_mail' => \Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction::class,
        ]);
    }
}
