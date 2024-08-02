<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\EditorConfiguration;
use Spatie\Mailcoach\Domain\Shared\Events\ServingMailcoach;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class BootstrapMailcoach
{
    use UsesMailcoachModels;

    public function handle($request, $next)
    {
        $this->bootstrapRouteModels();

        if (config('mailcoach.guard')) {
            config()->set('auth.defaults.guard', config('mailcoach.guard'));
        }

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('mailcoach.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Livewire::forceAssetInjection();

        Notification::configureUsing(function (Notification $notification): void {
            $notification->view('mailcoach::app.layouts.partials.notification');
        });

        ServingMailcoach::dispatch();

        $this->bootstrapEditors();

        return $next($request);
    }

    protected function bootstrapRouteModels(): void
    {
        // Audience
        Route::model('emailList', self::getEmailListClass());
        Route::model('subscriber', self::getSubscriberClass());
        Route::model('subscriberImport', self::getSubscriberImportClass());
        Route::model('tag', self::getTagClass());
        Route::model('tagSegment', self::getTagSegmentClass());
        Route::model('segment', self::getTagSegmentClass());
        Route::model('action', self::getAutomationActionClass());

        // Automation
        Route::model('action', self::getAutomationActionClass());
        Route::model('actionSubscriber', self::getActionSubscriberClass());
        Route::model('automation', self::getAutomationClass());
        Route::model('automationMail', self::getAutomationMailClass());
        Route::model('trigger', self::getAutomationTriggerClass());

        // Campaign
        Route::model('campaign', self::getCampaignClass());

        // Content
        Route::model('click', self::getClickClass());
        Route::model('link', self::getLinkClass());
        Route::model('open', self::getOpenClass());
        Route::model('unsubscribe', self::getUnsubscribeClass());
        Route::model('template', self::getTemplateClass());

        // Settings
        Route::model('mailer', self::getMailerClass());
        Route::model('setting', self::getSettingClass());
        Route::model('webhookConfiguration', self::getWebhookConfigurationClass());

        // Shared
        Route::model('send', self::getSendClass());
        Route::model('sendFeedbackItem', self::getSendFeedbackItemClass());
        Route::model('upload', self::getUploadClass());
        Route::model('webhook', self::getWebhookConfigurationClass());
        Route::model('webhookLog', self::getWebhookLogClass());

        // Transactional
        Route::model('transactionalMail', self::getTransactionalMailLogItemClass());
        Route::model('transactionalMailTemplate', self::getTransactionalMailClass());
    }

    protected function bootCodemirror(): void
    {
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\Codemirror\Editor::class, Vite::asset('resources/js/editors/codemirror/codemirror.js', 'vendor/mailcoach'));
    }

    protected function bootEditorJs(): void
    {
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/header@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/list@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/image@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/quote@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/raw@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/table@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/code@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest');
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class, 'https://cdn.jsdelivr.net/npm/editorjs-button@1.0.4');
    }

    protected function bootMarkdown(): void
    {
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\Markdown\Editor::class, Vite::asset('resources/js/editors/markdown/markdown.js', 'vendor/mailcoach'));
        Mailcoach::editorStyle(\Spatie\Mailcoach\Domain\Editor\Markdown\Editor::class, 'https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css');
    }

    protected function bootUnlayer(): void
    {
        Mailcoach::editorScript(\Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class, 'https://editor.unlayer.com/embed.js');
    }

    public function bootstrapEditors(): void
    {
        resolve(EditorConfiguration::class)->registerConfigValues();

        foreach ([config('mailcoach.content_editor'), config('mailcoach.template_editor')] as $usedEditor) {
            match ($usedEditor) {
                \Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class => $this->bootUnlayer(),
                \Spatie\Mailcoach\Domain\Editor\Codemirror\Editor::class => $this->bootCodemirror(),
                \Spatie\Mailcoach\Domain\Editor\EditorJs\Editor::class => $this->bootEditorJs(),
                \Spatie\Mailcoach\Domain\Editor\Markdown\Editor::class => $this->bootMarkdown(),
                default => null,
            };
        }
    }
}
