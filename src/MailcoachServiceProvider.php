<?php

namespace Spatie\Mailcoach;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\ReplacerHelpTextsComponent;
use Spatie\Mailcoach\Domain\Audience\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Domain\Audience\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Domain\Audience\Livewire\EmailListStatistics;
use Spatie\Mailcoach\Domain\Automation\Commands\CalculateAutomationMailStatisticsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\SendAutomationMailsCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredByEvents;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignMailsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddClickedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Content\Listeners\AddOpenedTag;
use Spatie\Mailcoach\Domain\Settings\Commands\PublishCommand;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookFailedAttemptsSubscriber;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookLogEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\Models\MailcoachUser;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Settings\Support\AppConfiguration\AppConfiguration;
use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\DeleteOldExportsCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Listeners\SetWebhookCallProcessedAt;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Commands\CalculateTransactionalStatisticsCommand;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Actions\AddMessageStreamHeader;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions\AddUniqueArgumentsMailHeader;
use Spatie\Mailcoach\Domain\Vendor\Ses\Actions\AddConfigurationSetHeader;
use Spatie\Mailcoach\Domain\Vendor\Ses\Actions\StoreTransportMessageId;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Brevo\BrevoWebhookController;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Mailgun\MailgunWebhookController;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Postmark\PostmarkWebhookController;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Resend\ResendWebhookController;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Sendgrid\SendgridWebhookController;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Ses\SesWebhookController;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapMailcoach;
use Spatie\Mailcoach\Http\App\ViewComposers\WebsiteStyleComposer;
use Spatie\Mailcoach\Livewire\Audience\CreateListComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateSegmentComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateSubscriberComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateTagComponent;
use Spatie\Mailcoach\Livewire\Audience\EmailListCountComponent;
use Spatie\Mailcoach\Livewire\Audience\ListOnboardingComponent;
use Spatie\Mailcoach\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSummaryComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentPopulationCountComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentsComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentSubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberExportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberSendsComponent;
use Spatie\Mailcoach\Livewire\Audience\TagComponent;
use Spatie\Mailcoach\Livewire\Audience\TagPopulationCountComponent;
use Spatie\Mailcoach\Livewire\Audience\TagsComponent;
use Spatie\Mailcoach\Livewire\Audience\WebsiteComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\AutomationMailActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\ConditionActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\SendWebhookActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\SplitActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\SubscribeToEmailListActionComponent;
use Spatie\Mailcoach\Livewire\Automations\Actions\WaitActionComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationBuilderComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSummaryComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationSubscribersComponent;
use Spatie\Mailcoach\Livewire\Automations\CreateAutomationComponent;
use Spatie\Mailcoach\Livewire\Automations\CreateAutomationMailComponent;
use Spatie\Mailcoach\Livewire\Automations\RunAutomationComponent;
use Spatie\Mailcoach\Livewire\Automations\Triggers\DateTriggerComponent;
use Spatie\Mailcoach\Livewire\Automations\Triggers\NoTriggerComponent;
use Spatie\Mailcoach\Livewire\Automations\Triggers\TagAddedTriggerComponent;
use Spatie\Mailcoach\Livewire\Automations\Triggers\TagRemovedTriggerComponent;
use Spatie\Mailcoach\Livewire\Automations\Triggers\WebhookTriggerComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignStatisticsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSummaryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CreateCampaignComponent;
use Spatie\Mailcoach\Livewire\Campaigns\OutboxComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionBuilderComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberAttributesConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberEmailQueryConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedAutomationMailConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedCampaignConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberReceivedCampaignConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberSubscribedAtConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberTagsConditionComponent;
use Spatie\Mailcoach\Livewire\Content\ClicksComponent;
use Spatie\Mailcoach\Livewire\Content\EditContentComponent;
use Spatie\Mailcoach\Livewire\Content\LinkClicksComponent;
use Spatie\Mailcoach\Livewire\Content\OpensComponent;
use Spatie\Mailcoach\Livewire\Content\UnsubscribesComponent;
use Spatie\Mailcoach\Livewire\Dashboard\DashboardChart;
use Spatie\Mailcoach\Livewire\Dashboard\DashboardComponent;
use Spatie\Mailcoach\Livewire\Editor\EditorSettingsComponent;
use Spatie\Mailcoach\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Livewire\LinkCheckComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Brevo\BrevoSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Mailgun\MailgunSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Postmark\PostmarkSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Resend\ResendSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\SendGrid\SendGridSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Ses\SesSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Smtp\SmtpSetupWizardComponent;
use Spatie\Mailcoach\Livewire\Mailers\CreateMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\EditMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\MailersComponent;
use Spatie\Mailcoach\Livewire\Mails\CreateSuppressionComponent;
use Spatie\Mailcoach\Livewire\Mails\SuppressionListComponent;
use Spatie\Mailcoach\Livewire\SendTestComponent;
use Spatie\Mailcoach\Livewire\Templates\CreateTemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\CreateTransactionalTemplateComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateContentComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSummaryComponent;
use Spatie\Mailcoach\Livewire\Webhooks\CreateWebhookComponent;
use Spatie\Mailcoach\Livewire\Webhooks\EditWebhookComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhookLogComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhookLogsComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent;
use Spatie\Navigation\Helpers\ActiveUrlChecker;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class MailcoachServiceProvider extends PackageServiceProvider
{
    use UsesMailcoachModels;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailcoach')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                'create_mailcoach_tables',
                'create_media_table',
                'create_webhook_calls_table',
            ])
            ->hasCommands([
                CalculateStatisticsCommand::class,
                CalculateAutomationMailStatisticsCommand::class,
                CalculateTransactionalStatisticsCommand::class,
                SendAutomationMailsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignMailsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                CleanupProcessedFeedbackCommand::class,
                RunAutomationActionsCommand::class,
                RunAutomationTriggersCommand::class,
                CheckLicenseCommand::class,
                DeleteOldExportsCommand::class,
                PublishCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Version::class, fn () => new Version());

        $this->app->scoped(MainNavigation::class, function () {
            return new MainNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SettingsNavigation::class, function () {
            return new SettingsNavigation(app(ActiveUrlChecker::class));
        });

        foreach (Mailcoach::defaultModels() as $key => $defaultModelClass) {
            app()->bind($defaultModelClass, config("mailcoach.models.{$key}"));

            Relation::morphMap([$key => config("mailcoach.models.{$key}")]);
        }
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
            ], "{$this->package->shortName()}-assets");
        }

        $this
            ->bootCarbon()
            ->bootConfig()
            ->bootGate()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers()
            ->bootMailgun()
            ->bootPostmark()
            ->bootSendgrid()
            ->bootBrevo()
            ->bootSes()
            ->bootOctane();
    }

    protected function bootCarbon(): static
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro(
            'toMailcoachFormat',
            /** @phpstan-ignore-next-line */
            fn () => self::this()->copy()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->format($mailcoachFormat)
        );

        return $this;
    }

    protected function bootConfig(): static
    {
        if (! config('mailcoach.boot_config', true)) {
            return $this;
        }

        try {
            self::getMailerClass()::registerAllConfigValues();
        } catch (QueryException) {
            // Do nothing as table probably doesn't exist
        }

        app(AppConfiguration::class)->registerConfigValues();

        return $this;
    }

    protected function bootSupportMacros(): static
    {
        if (! Str::hasMacro('shortNumber')) {
            Str::macro('shortNumber', function (int $number, int $decimals = 1) {
                if ($number < 1_000) {
                    $format = number_format($number, $decimals);
                    $suffix = '';
                } elseif ($number < 1_000_000) {
                    $format = number_format(floor($number / 100) / 10, $decimals);
                    $suffix = 'K';
                } elseif ($number < 1_000_000_000) {
                    $format = number_format(floor($number / 100000) / 10, $decimals);
                    $suffix = 'M';
                } else {
                    return '🤯';
                }

                if ($decimals > 0) {
                    $dotzero = '.'.str_repeat('0', $decimals);
                    $format = str_replace($dotzero, '', $format);
                }

                return $format.$suffix;
            });
        }

        return $this;
    }

    protected function bootGate(): static
    {
        Gate::define('viewMailcoach', fn (MailcoachUser $user) => $user->canViewMailcoach());

        return $this;
    }

    protected function bootRoutes(): static
    {
        Route::macro('brevoFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.BrevoWebhookController::class));
        Route::macro('mailgunFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.MailgunWebhookController::class));
        Route::macro('postmarkFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.PostmarkWebhookController::class));
        Route::macro('resendFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.ResendWebhookController::class));
        Route::macro('sendgridFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.SendgridWebhookController::class));
        Route::macro('sesFeedback', fn (string $url) => Route::post("{$url}/{mailerConfigKey?}", '\\'.SesWebhookController::class));

        Route::macro('mailcoach', function (
            string $url = '',
            bool $registerFeedback = true,
        ) {
            Route::middleware([BootstrapMailcoach::class])->group(function () use ($url, $registerFeedback) {
                if ($registerFeedback) {
                    Route::namespace(null)->group(function () {
                        Route::brevoFeedback('brevo-feedback');
                        Route::mailgunFeedback('mailgun-feedback');
                        Route::postmarkFeedback('postmark-feedback');
                        Route::resendFeedback('resend-feedback');
                        Route::sendgridFeedback('sendgrid-feedback');
                        Route::sesFeedback('ses-feedback');
                    });
                }

                Route::prefix($url)->namespace(null)->group(function () {
                    Route::prefix('')
                        ->group(__DIR__.'/../routes/mailcoach-public-api.php');

                    Route::prefix('')
                        ->middleware(config('mailcoach.middleware')['web'])
                        ->group(__DIR__.'/../routes/mailcoach-ui.php');

                    Route::prefix('api')
                        ->middleware(config('mailcoach.middleware')['api'])
                        ->group(__DIR__.'/../routes/mailcoach-api.php');

                    /*
                     * The website routes should be registered last, so that
                     * they don't eat up other routes
                     */
                    Route::prefix(config('mailcoach.website_prefix', 'archive'))
                        ->middleware('web')
                        ->group(__DIR__.'/../routes/mailcoach-email-list-website.php');
                });

                Route::get($url, function () {
                    return redirect()->route(config('mailcoach.redirect_home', 'mailcoach.dashboard'));
                })->name('mailcoach.home');
            });
        });

        return $this;
    }

    protected function bootViews(): static
    {
        View::composer('mailcoach::emailListWebsite.partials.style', WebsiteStyleComposer::class);

        $this->bootBladeComponents();
        $this->bootLivewireComponents();

        return $this;
    }

    protected function bootMailgun(): static
    {
        Event::listen(MessageSent::class, \Spatie\Mailcoach\Domain\Vendor\Mailgun\Actions\StoreTransportMessageId::class);

        return $this;
    }

    protected function bootPostmark(): static
    {
        Event::listen(MessageSending::class, AddMessageStreamHeader::class);

        return $this;
    }

    protected function bootSendgrid(): static
    {
        Event::listen(MessageSending::class, AddUniqueArgumentsMailHeader::class);
        Event::listen(MessageSent::class, \Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions\StoreTransportMessageId::class);

        Mail::extend('sendgrid', function (array $config) {
            $key = $config['key'] ?? config('services.sendgrid.key');

            return (new SendgridTransportFactory())->create(
                Dsn::fromString("sendgrid+api://{$key}@default")
            );
        });

        return $this;
    }

    protected function bootBrevo(): static
    {
        Event::listen(MessageSent::class, \Spatie\Mailcoach\Domain\Vendor\Brevo\Actions\StoreTransportMessageId::class);

        Mail::extend('brevo', function (array $config) {
            $key = $config['key'] ?? config('services.brevo.key') ?? config('services.sendinblue.key');

            return (new BrevoTransportFactory())->create(
                Dsn::fromString("brevo+api://{$key}@default")
            );
        });

        return $this;
    }

    protected function bootSes(): static
    {
        Event::listen(MessageSending::class, AddConfigurationSetHeader::class);
        Event::listen(MessageSent::class, StoreTransportMessageId::class);

        return $this;
    }

    protected function bootBladeComponents(): static
    {
        Blade::component('mailcoach::app.components.form.checkboxField', 'mailcoach::checkbox-field');
        Blade::component('mailcoach::app.components.form.radioField', 'mailcoach::radio-field');
        Blade::component('mailcoach::app.components.form.formButton', 'mailcoach::form-button');
        Blade::component('mailcoach::app.components.form.formButtons', 'mailcoach::form-buttons');
        Blade::component('mailcoach::app.components.form.confirmButton', 'mailcoach::confirm-button');
        Blade::component('mailcoach::app.components.form.selectField', 'mailcoach::select-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'mailcoach::tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'mailcoach::text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'mailcoach::html-field');
        Blade::component('mailcoach::app.components.form.markdownField', 'mailcoach::markdown-field');
        Blade::component('mailcoach::app.components.form.colorField', 'mailcoach::color-field');
        Blade::component('mailcoach::app.components.form.textArea', 'mailcoach::textarea-field');
        Blade::component('mailcoach::app.components.form.upload', 'mailcoach::upload-field');

        Blade::component('mailcoach::app.components.form.templateChooser', 'mailcoach::template-chooser');

        Blade::component('mailcoach::app.components.form.dateField', 'mailcoach::date-field');
        Blade::component('mailcoach::app.components.form.fieldset', 'mailcoach::fieldset');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');
        Blade::component('mailcoach::app.components.modal.previewModal', 'mailcoach::preview-modal');

        Blade::component('mailcoach::app.components.apiCard', 'mailcoach::api-card');
        Blade::component('mailcoach::app.components.card', 'mailcoach::card');
        Blade::component('mailcoach::app.components.tile', 'mailcoach::tile');

        Blade::component('mailcoach::app.components.statistic', 'mailcoach::statistic');
        Blade::component('mailcoach::app.components.healthLabel', 'mailcoach::health-label');
        Blade::component('mailcoach::app.components.roundedIcon', 'mailcoach::rounded-icon');
        Blade::component('mailcoach::app.components.imageUpload', 'mailcoach::image-upload');

        Blade::component('mailcoach::app.components.navigation.main', 'mailcoach::main-navigation');
        Blade::component('mailcoach::app.components.navigation.root', 'mailcoach::navigation');
        Blade::component('mailcoach::app.components.navigation.item', 'mailcoach::navigation-item');
        Blade::component('mailcoach::app.components.navigation.group', 'mailcoach::navigation-group');

        Blade::component('mailcoach::app.components.alert', 'mailcoach::alert');
        Blade::component('mailcoach::app.components.tag', 'mailcoach::tag');

        Blade::component('mailcoach::app.components.addressDefinition', 'mailcoach::address-definition');
        Blade::component('mailcoach::app.components.webview', 'mailcoach::web-view');
        Blade::component('mailcoach::app.components.sniper-link', 'mailcoach::sniper-link');

        Blade::component('mailcoach::app.components.button.primary', 'mailcoach::button');
        Blade::component('mailcoach::app.components.button.secondary', 'mailcoach::button-secondary');
        Blade::component('mailcoach::app.components.button.tertiary', 'mailcoach::button-tertiary');
        Blade::component('mailcoach::app.components.button.link', 'mailcoach::button-link');

        Blade::component('mailcoach::app.components.editorButtons', 'mailcoach::editor-buttons');
        Blade::component('mailcoach::app.components.editorFields', 'mailcoach::editor-fields');
        Blade::component(ReplacerHelpTextsComponent::class, 'mailcoach::replacer-help-texts');

        Blade::component('mailcoach::app.components.dropdown', 'mailcoach::dropdown');

        Blade::component('mailcoach::app.layouts.app', 'mailcoach::layout');
        Blade::component('mailcoach::app.layouts.settings', 'mailcoach::layout-settings');
        Blade::component('mailcoach::app.campaigns.components.checklistItem', 'mailcoach::checklist-item');

        Blade::component('mailcoach::app.automations.components.automationAction', 'mailcoach::automation-action');
        Blade::component('mailcoach::app.conditionBuilder.conditions.condition', 'mailcoach::condition');

        Blade::component('mailcoach::app.components.code', 'mailcoach::code');
        Blade::component('mailcoach::app.components.codeCopy', 'mailcoach::code-copy');

        Blade::component('mailcoach::emailListWebsite.layouts.emailListWebsite', 'mailcoach::layout-website');

        return $this;
    }

    protected function bootLivewireComponents(): static
    {
        Livewire::addPersistentMiddleware([
            BootstrapMailcoach::class,
        ]);

        Livewire::component('mailcoach::email-list-count', Mailcoach::getLivewireClass(EmailListCountComponent::class));
        Livewire::component('mailcoach::segment-population-count', Mailcoach::getLivewireClass(SegmentPopulationCountComponent::class));
        Livewire::component('mailcoach::tag-population-count', Mailcoach::getLivewireClass(TagPopulationCountComponent::class));
        Livewire::component('mailcoach::link-check', Mailcoach::getLivewireClass(LinkCheckComponent::class));

        Livewire::component('mailcoach::automation-builder', Mailcoach::getLivewireClass(AutomationBuilderComponent::class));

        Livewire::component('mailcoach::automation-action', Mailcoach::getLivewireClass(AutomationActionComponent::class));
        Livewire::component('mailcoach::automation-mail-action', Mailcoach::getLivewireClass(AutomationMailActionComponent::class));
        Livewire::component('mailcoach::add-tags-action', Mailcoach::getLivewireClass(AddTagsActionComponent::class));
        Livewire::component('mailcoach::remove-tags-action', Mailcoach::getLivewireClass(RemoveTagsActionComponent::class));
        Livewire::component('mailcoach::wait-action', Mailcoach::getLivewireClass(WaitActionComponent::class));
        Livewire::component('mailcoach::condition-action', Mailcoach::getLivewireClass(ConditionActionComponent::class));
        Livewire::component('mailcoach::split-action', Mailcoach::getLivewireClass(SplitActionComponent::class));
        Livewire::component('mailcoach::send-webhook-action', Mailcoach::getLivewireClass(SendWebhookActionComponent::class));
        Livewire::component('mailcoach::email-list-action', Mailcoach::getLivewireClass(SubscribeToEmailListActionComponent::class));

        Livewire::component('mailcoach::date-trigger', Mailcoach::getLivewireClass(DateTriggerComponent::class));
        Livewire::component('mailcoach::tag-added-trigger', Mailcoach::getLivewireClass(TagAddedTriggerComponent::class));
        Livewire::component('mailcoach::tag-removed-trigger', Mailcoach::getLivewireClass(TagRemovedTriggerComponent::class));
        Livewire::component('mailcoach::webhook-trigger', Mailcoach::getLivewireClass(WebhookTriggerComponent::class));
        Livewire::component('mailcoach::no-trigger', Mailcoach::getLivewireClass(NoTriggerComponent::class));

        Livewire::component('mailcoach::email-list-statistics', Mailcoach::getLivewireClass(EmailListStatistics::class));
        Livewire::component('mailcoach::campaign-statistics', Mailcoach::getLivewireClass(CampaignStatisticsComponent::class));

        Livewire::component('mailcoach::send-test', Mailcoach::getLivewireClass(SendTestComponent::class));

        Livewire::component('mailcoach::dashboard', Mailcoach::getLivewireClass(DashboardComponent::class));
        Livewire::component('mailcoach::dashboard-chart', Mailcoach::getLivewireClass(DashboardChart::class));

        // Audience
        Livewire::component('mailcoach::create-list', Mailcoach::getLivewireClass(CreateListComponent::class));
        Livewire::component('mailcoach::lists', Mailcoach::getLivewireClass(ListsComponent::class));
        Livewire::component('mailcoach::list-summary', Mailcoach::getLivewireClass(ListSummaryComponent::class));
        Livewire::component('mailcoach::list-settings', Mailcoach::getLivewireClass(ListSettingsComponent::class));
        Livewire::component('mailcoach::list-onboarding', Mailcoach::getLivewireClass(ListOnboardingComponent::class));
        Livewire::component('mailcoach::website', Mailcoach::getLivewireClass(WebsiteComponent::class));
        Livewire::component('mailcoach::create-segment', Mailcoach::getLivewireClass(CreateSegmentComponent::class));
        Livewire::component('mailcoach::segments', Mailcoach::getLivewireClass(SegmentsComponent::class));
        Livewire::component('mailcoach::segment', Mailcoach::getLivewireClass(SegmentComponent::class));
        Livewire::component('mailcoach::segment-subscribers', Mailcoach::getLivewireClass(SegmentSubscribersComponent::class));
        Livewire::component('mailcoach::create-subscriber', Mailcoach::getLivewireClass(CreateSubscriberComponent::class));
        Livewire::component('mailcoach::subscribers', Mailcoach::getLivewireClass(SubscribersComponent::class));
        Livewire::component('mailcoach::subscriber', Mailcoach::getLivewireClass(SubscriberComponent::class));
        Livewire::component('mailcoach::subscriber-sends', Mailcoach::getLivewireClass(SubscriberSendsComponent::class));
        Livewire::component('mailcoach::subscriber-imports', Mailcoach::getLivewireClass(SubscriberImportsComponent::class));
        Livewire::component('mailcoach::subscriber-exports', Mailcoach::getLivewireClass(SubscriberExportsComponent::class));
        Livewire::component('mailcoach::create-tag', Mailcoach::getLivewireClass(CreateTagComponent::class));
        Livewire::component('mailcoach::tags', Mailcoach::getLivewireClass(TagsComponent::class));
        Livewire::component('mailcoach::tag', Mailcoach::getLivewireClass(TagComponent::class));

        // Automations
        Livewire::component('mailcoach::create-automation', Mailcoach::getLivewireClass(CreateAutomationComponent::class));
        Livewire::component('mailcoach::automations', Mailcoach::getLivewireClass(AutomationsComponent::class));
        Livewire::component('mailcoach::automation-settings', Mailcoach::getLivewireClass(AutomationSettingsComponent::class));
        Livewire::component('mailcoach::automation-subscribers', Mailcoach::getLivewireClass(AutomationSubscribersComponent::class));
        Livewire::component('mailcoach::automation-actions', Mailcoach::getLivewireClass(AutomationActionsComponent::class));
        Livewire::component('mailcoach::automation-run', Mailcoach::getLivewireClass(RunAutomationComponent::class));
        Livewire::component('mailcoach::create-automation-mail', Mailcoach::getLivewireClass(CreateAutomationMailComponent::class));
        Livewire::component('mailcoach::automation-mails', Mailcoach::getLivewireClass(AutomationMailsComponent::class));
        Livewire::component('mailcoach::automation-mail-summary', Mailcoach::getLivewireClass(AutomationMailSummaryComponent::class));
        Livewire::component('mailcoach::automation-mail-settings', Mailcoach::getLivewireClass(AutomationMailSettingsComponent::class));

        // Campaigns
        Livewire::component('mailcoach::create-campaign', Mailcoach::getLivewireClass(CreateCampaignComponent::class));
        Livewire::component('mailcoach::campaigns', Mailcoach::getLivewireClass(CampaignsComponent::class));
        Livewire::component('mailcoach::create-template', Mailcoach::getLivewireClass(CreateTemplateComponent::class));
        Livewire::component('mailcoach::templates', Mailcoach::getLivewireClass(TemplatesComponent::class));
        Livewire::component('mailcoach::template', Mailcoach::getLivewireClass(TemplateComponent::class));
        Livewire::component('mailcoach::campaign-settings', Mailcoach::getLivewireClass(CampaignSettingsComponent::class));
        Livewire::component('mailcoach::campaign-delivery', Mailcoach::getLivewireClass(CampaignDeliveryComponent::class));
        Livewire::component('mailcoach::campaign-summary', Mailcoach::getLivewireClass(CampaignSummaryComponent::class));

        // Content
        Livewire::component('mailcoach::content', Mailcoach::getLivewireClass(EditContentComponent::class));
        Livewire::component('mailcoach::opens', Mailcoach::getLivewireClass(OpensComponent::class));
        Livewire::component('mailcoach::clicks', Mailcoach::getLivewireClass(ClicksComponent::class));
        Livewire::component('mailcoach::link-clicks', Mailcoach::getLivewireClass(LinkClicksComponent::class));
        Livewire::component('mailcoach::unsubscribes', Mailcoach::getLivewireClass(UnsubscribesComponent::class));
        Livewire::component('mailcoach::outbox', Mailcoach::getLivewireClass(OutboxComponent::class));

        // TransactionalMails
        Livewire::component('mailcoach::create-transactional-template', Mailcoach::getLivewireClass(CreateTransactionalTemplateComponent::class));
        Livewire::component('mailcoach::transactional-mails', Mailcoach::getLivewireClass(TransactionalMailLogItemsComponent::class));
        Livewire::component('mailcoach::transactional-mail-templates', Mailcoach::getLivewireClass(TransactionalMailsComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-summary', Mailcoach::getLivewireClass(TransactionalTemplateSummaryComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-content', Mailcoach::getLivewireClass(TransactionalTemplateContentComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-settings', Mailcoach::getLivewireClass(TransactionalTemplateSettingsComponent::class));
        Livewire::component('mailcoach::transactional-mail', Mailcoach::getLivewireClass(TransactionalMailComponent::class));

        Livewire::component('mailcoach::export', Mailcoach::getLivewireClass(ExportComponent::class));
        Livewire::component('mailcoach::import', Mailcoach::getLivewireClass(ImportComponent::class));

        // settings
        Livewire::component('mailcoach::webhooks', Mailcoach::getLivewireClass(WebhooksComponent::class));
        Livewire::component('mailcoach::create-webhook', Mailcoach::getLivewireClass(CreateWebhookComponent::class));
        Livewire::component('mailcoach::edit-webhook', Mailcoach::getLivewireClass(EditWebhookComponent::class));
        Livewire::component('mailcoach::webhook-logs', Mailcoach::getLivewireClass(WebhookLogsComponent::class));
        Livewire::component('mailcoach::webhook-log', Mailcoach::getLivewireClass(WebhookLogComponent::class));

        Livewire::component('mailcoach::mailers', Mailcoach::getLivewireClass(MailersComponent::class));
        Livewire::component('mailcoach::create-mailer', Mailcoach::getLivewireClass(CreateMailerComponent::class));
        Livewire::component('mailcoach::general-settings', Mailcoach::getLivewireClass(GeneralSettingsComponent::class));
        Livewire::component('mailcoach::editor-settings', Mailcoach::getLivewireClass(EditorSettingsComponent::class));
        Livewire::component('mailcoach::mailer-send-test', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\MailConfiguration\SendTestComponent::class));
        Livewire::component('mailcoach::edit-mailer', Mailcoach::getLivewireClass(EditMailerComponent::class));
        Livewire::component('mailcoach::suppression-list', Mailcoach::getLivewireClass(SuppressionListComponent::class));
        Livewire::component('mailcoach::create-suppression', Mailcoach::getLivewireClass(CreateSuppressionComponent::class));

        // Editors
        Livewire::component('mailcoach::editor-codemirror', Domain\Editor\Codemirror\Editor::class);
        Livewire::component('mailcoach::editor-unlayer', Domain\Editor\Unlayer\Editor::class);
        Livewire::component('mailcoach::editor-editorjs', Domain\Editor\EditorJs\Editor::class);
        Livewire::component('mailcoach::editor-markdown', Domain\Editor\Markdown\Editor::class);

        // Condition builder
        Livewire::component('mailcoach::condition-builder', Mailcoach::getLivewireClass(ConditionBuilderComponent::class));
        Livewire::component('mailcoach::subscriber-tags-condition', Mailcoach::getLivewireClass(SubscriberTagsConditionComponent::class));
        Livewire::component('mailcoach::subscriber-subscribed-at-condition', Mailcoach::getLivewireClass(SubscriberSubscribedAtConditionComponent::class));
        Livewire::component('mailcoach::subscriber-attributes-condition', Mailcoach::getLivewireClass(SubscriberAttributesConditionComponent::class));
        Livewire::component('mailcoach::subscriber-opened-campaign-condition', Mailcoach::getLivewireClass(SubscriberOpenedCampaignConditionComponent::class));
        Livewire::component('mailcoach::subscriber-received-campaign-condition', Mailcoach::getLivewireClass(SubscriberReceivedCampaignConditionComponent::class));
        Livewire::component('mailcoach::subscriber-opened-automation-mail-condition', Mailcoach::getLivewireClass(SubscriberOpenedAutomationMailConditionComponent::class));
        Livewire::component('mailcoach::subscriber-clicked-automation-mail-link-condition', Mailcoach::getLivewireClass(SubscriberClickedAutomationMailLinkConditionComponent::class));
        Livewire::component('mailcoach::subscriber-clicked-campaign-link-condition', Mailcoach::getLivewireClass(SubscriberClickedCampaignLinkConditionComponent::class));
        Livewire::component('mailcoach::subscriber-email-condition', Mailcoach::getLivewireClass(SubscriberEmailQueryConditionComponent::class));

        BrevoSetupWizardComponent::registerLivewireComponents();
        MailgunSetupWizardComponent::registerLivewireComponents();
        PostmarkSetupWizardComponent::registerLivewireComponents();
        ResendSetupWizardComponent::registerLivewireComponents();
        SendGridSetupWizardComponent::registerLivewireComponents();
        SesSetupWizardComponent::registerLivewireComponents();
        SmtpSetupWizardComponent::registerLivewireComponents();

        return $this;
    }

    protected function bootEvents(): static
    {
        Event::listen(CampaignSentEvent::class, SendCampaignSentEmail::class);
        Event::listen(WebhookCallProcessedEvent::class, SetWebhookCallProcessedAt::class);
        Event::listen(MessageSending::class, StoreTransactionalMail::class);
        Event::listen(ContentOpenedEvent::class, AddOpenedTag::class);
        Event::listen(LinkClickedEvent::class, AddClickedTag::class);

        Event::subscribe(WebhookEventSubscriber::class);
        Event::subscribe(WebhookLogEventSubscriber::class);
        Event::subscribe(WebhookFailedAttemptsSubscriber::class);

        return $this;
    }

    protected function bootTriggers(): static
    {
        if (! config('mailcoach.boot_triggers', true)) {
            return $this;
        }

        try {
            $triggers = cache()->rememberForever('automation-triggers', function () {
                return static::getAutomationTriggerClass()::with(['automation'])->has('automation')->get();
            });

            $triggers
                ->filter(fn (Trigger $trigger) => $trigger->trigger instanceof TriggeredByEvents)
                ->each(function (Trigger $trigger) {
                    if ($trigger->automation) {
                        Event::subscribe($trigger->trigger->setAutomation($trigger->automation));
                    }
                });
        } catch (Exception) {
            // Do nothing as the database is probably not set up yet.
        }

        return $this;
    }

    protected function bootOctane(): void
    {
        $this->app['events']->listen(\Laravel\Octane\Events\RequestReceived::class, function () {
            Mailcoach::flushState();
        });

        $this->app['events']->listen(\Laravel\Octane\Events\TaskReceived::class, function () {
            Mailcoach::flushState();
        });

        $this->app['events']->listen(\Laravel\Octane\Events\TickReceived::class, function () {
            Mailcoach::flushState();
        });

        $this->app['events']->listen(\Laravel\Octane\Events\RequestTerminated::class, function () {
            Mailcoach::flushState();
        });
    }
}
