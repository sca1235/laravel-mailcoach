<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Editors\EditorJs\RenderEditorController;
use Spatie\Mailcoach\Http\Api\Controllers\Editors\Markdown\RenderMarkdownController;
use Spatie\Mailcoach\Http\Api\Controllers\UploadsController;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapSettingsNavigation;
use Spatie\Mailcoach\Livewire\Audience\ListOnboardingComponent;
use Spatie\Mailcoach\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSummaryComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberExportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\TagComponent;
use Spatie\Mailcoach\Livewire\Audience\TagsComponent;
use Spatie\Mailcoach\Livewire\Audience\WebsiteComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailDeliveryComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSummaryComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationSubscribersComponent;
use Spatie\Mailcoach\Livewire\Automations\RunAutomationComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSummaryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\OutboxComponent;
use Spatie\Mailcoach\Livewire\Content\ClicksComponent;
use Spatie\Mailcoach\Livewire\Content\EditContentComponent;
use Spatie\Mailcoach\Livewire\Content\LinkClicksComponent;
use Spatie\Mailcoach\Livewire\Content\OpensComponent;
use Spatie\Mailcoach\Livewire\Content\UnsubscribesComponent;
use Spatie\Mailcoach\Livewire\Dashboard\DashboardComponent;
use Spatie\Mailcoach\Livewire\DebugComponent;
use Spatie\Mailcoach\Livewire\Editor\EditorSettingsComponent;
use Spatie\Mailcoach\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Livewire\Mailers\EditMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\MailersComponent;
use Spatie\Mailcoach\Livewire\Mails\SuppressionListComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateContentComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSummaryComponent;
use Spatie\Mailcoach\Livewire\Webhooks\EditWebhookComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhookLogComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent;
use Spatie\Mailcoach\Mailcoach;

Route::get('dashboard', Mailcoach::getLivewireClass(DashboardComponent::class))->name('mailcoach.dashboard');
Route::get('debug', Mailcoach::getLivewireClass(DebugComponent::class))->name('debug');

Route::post('uploads', UploadsController::class);

Route::get('export', ExportComponent::class)->name('export');
Route::get('import', ImportComponent::class)->name('import');

Route::post('render', '\\'.RenderEditorController::class);
Route::post('render-markdown', '\\'.RenderMarkdownController::class)->name('mailcoach-markdown-editor.render-markdown');

Route::prefix('campaigns')->group(function () {
    Route::get('/', Mailcoach::getLivewireClass(CampaignsComponent::class))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->name('mailcoach.campaigns.')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass(CampaignSettingsComponent::class))->name('settings');
        Route::get('content', Mailcoach::getLivewireClass(EditContentComponent::class))->name('content');
        Route::get('delivery', Mailcoach::getLivewireClass(CampaignDeliveryComponent::class))->name('delivery');

        Route::get('summary', Mailcoach::getLivewireClass(CampaignSummaryComponent::class))->name('summary');
        Route::get('opens', Mailcoach::getLivewireClass(OpensComponent::class))->name('opens');
        Route::get('clicks', Mailcoach::getLivewireClass(ClicksComponent::class))->name('clicks');
        Route::get('clicks/{linkUuids}', Mailcoach::getLivewireClass(LinkClicksComponent::class))->name('link-clicks');
        Route::get('unsubscribes', Mailcoach::getLivewireClass(UnsubscribesComponent::class))->name('unsubscribes');
        Route::get('outbox', Mailcoach::getLivewireClass(OutboxComponent::class))->name('outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', Mailcoach::getLivewireClass(ListsComponent::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->name('mailcoach.emailLists.')->group(function () {
        Route::get('summary', Mailcoach::getLivewireClass(ListSummaryComponent::class))->name('summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', Mailcoach::getLivewireClass(SubscribersComponent::class))->name('subscribers');
            Route::get('{subscriber}', Mailcoach::getLivewireClass(SubscriberComponent::class))->name('subscriber.details');
        });

        Route::get('import-subscribers', Mailcoach::getLivewireClass(SubscriberImportsComponent::class))->name('import-subscribers');
        Route::get('subscriber-exports', Mailcoach::getLivewireClass(SubscriberExportsComponent::class))->name('subscriber-exports');

        Route::get('settings', Mailcoach::getLivewireClass(ListSettingsComponent::class))->name('general-settings');
        Route::get('onboarding', Mailcoach::getLivewireClass(ListOnboardingComponent::class))->name('onboarding');

        if (config('mailcoach.audience.website', true)) {
            Route::get('website', Mailcoach::getLivewireClass(WebsiteComponent::class))->name('website');
        }

        Route::prefix('tags')->group(function () {
            Route::get('/', Mailcoach::getLivewireClass(TagsComponent::class))->name('tags');
            Route::get('{tag}', Mailcoach::getLivewireClass(TagComponent::class))->name('tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', Mailcoach::getLivewireClass(SegmentsComponent::class))->name('segments');
            Route::get('{segment}', Mailcoach::getLivewireClass(SegmentComponent::class))->name('segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::view('/', 'mailcoach::app.automations.index')->name('mailcoach.automations');

    Route::prefix('{automation}')->name('mailcoach.automations.')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass(AutomationSettingsComponent::class))->name('settings');
        Route::get('run', Mailcoach::getLivewireClass(RunAutomationComponent::class))->name('run');
        Route::get('actions', Mailcoach::getLivewireClass(AutomationActionsComponent::class))->name('actions');
        Route::get('subscribers', Mailcoach::getLivewireClass(AutomationSubscribersComponent::class))->name('subscribers');
    });

    Route::prefix('emails/{automationMail}')->name('mailcoach.automations.mails.')->group(function () {
        Route::get('summary', Mailcoach::getLivewireClass(AutomationMailSummaryComponent::class))->name('summary');
        Route::get('settings', Mailcoach::getLivewireClass(AutomationMailSettingsComponent::class))->name('settings');
        Route::get('content', Mailcoach::getLivewireClass(EditContentComponent::class))->name('content');
        Route::get('delivery', Mailcoach::getLivewireClass(AutomationMailDeliveryComponent::class))->name('delivery');

        Route::get('opens', Mailcoach::getLivewireClass(OpensComponent::class))->name('opens');
        Route::get('clicks', Mailcoach::getLivewireClass(ClicksComponent::class))->name('clicks');
        Route::get('clicks/{linkUuids}', Mailcoach::getLivewireClass(LinkClicksComponent::class))->name('link-clicks');
        Route::get('unsubscribes', Mailcoach::getLivewireClass(UnsubscribesComponent::class))->name('unsubscribes');
        Route::get('outbox', Mailcoach::getLivewireClass(OutboxComponent::class))->name('outbox');
    });
});

Route::prefix('transactional')->group(function () {
    Route::view('/', 'mailcoach::app.transactionalMails.index')->name('mailcoach.transactional');

    Route::prefix('log')->group(function () {
        Route::prefix('{transactionalMail}')->name('mailcoach.transactionalMails.')->group(function () {
            Route::get('/', Mailcoach::getLivewireClass(TransactionalMailComponent::class))->name('show');
        });
    });

    Route::prefix('emails')->group(function () {
        Route::prefix('{transactionalMailTemplate}')->name('mailcoach.transactionalMails.templates.')->group(function () {
            Route::get('summary', Mailcoach::getLivewireClass(TransactionalTemplateSummaryComponent::class))->name('summary');
            Route::get('content', Mailcoach::getLivewireClass(TransactionalTemplateContentComponent::class))->name('edit');
            Route::get('settings', Mailcoach::getLivewireClass(TransactionalTemplateSettingsComponent::class))->name('settings');
        });
    });
});

Route::prefix('templates')->group(function () {
    Route::get('/', Mailcoach::getLivewireClass(TemplatesComponent::class))->name('mailcoach.templates');
    Route::get('{template}', Mailcoach::getLivewireClass(TemplateComponent::class))->name('mailcoach.templates.edit');
});

Route::prefix('settings')
    ->middleware([BootstrapSettingsNavigation::class])
    ->group(function () {
        Route::get('general', GeneralSettingsComponent::class)->name('general-settings');

        Route::prefix('mailers')->group(function () {
            Route::get('/', MailersComponent::class)->name('mailers');
            Route::get('{mailer}', EditMailerComponent::class)->name('mailers.edit');
        });

        Route::get('suppressions', Mailcoach::getLivewireClass(SuppressionListComponent::class))->name('suppressions');
        Route::get('editor', EditorSettingsComponent::class)->name('editor');

        Route::prefix('webhooks')->group(function () {
            Route::get('/', Mailcoach::getLivewireClass(WebhooksComponent::class))->name('webhooks');
            Route::get('{webhook}', Mailcoach::getLivewireClass(EditWebhookComponent::class))->name('webhooks.edit');
            Route::get('{webhook}/logs/{webhookLog}', Mailcoach::getLivewireClass(WebhookLogComponent::class))->name('webhooks.logs.show');
        });
    });
