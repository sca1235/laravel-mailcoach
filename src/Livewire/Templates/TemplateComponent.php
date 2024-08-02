<?php

namespace Spatie\Mailcoach\Livewire\Templates;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template as TemplateModel;
use Spatie\Mailcoach\Domain\Template\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Mailcoach;

class TemplateComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TemplateModel $template;

    #[Validate('required')]
    public ?string $name;

    #[Validate('required')]
    public ?string $html;

    public function mount(TemplateModel $template)
    {
        $this->authorize('update', $template);

        $this->template = $template;
        $this->name = $template->name;
        $this->html = $template->getHtml();
    }

    #[On('updateTemplateFieldValues')]
    public function updateHtml(mixed $html, mixed $json)
    {
        $this->html = $html;
    }

    public function save()
    {
        $this->validate();

        $this->dispatch('saveContent');
    }

    #[On('editorSaved')]
    public function updateTemplate()
    {
        $this->template->refresh();

        $this->template->name = $this->name;
        $this->template->save();

        $this->reRenderEmailsUsingTemplate();

        notify(__mc('Template :template was updated.', ['template' => $this->template->name]));
    }

    #[On('editorUpdated')]
    public function updatePreviewHtml($uuid, $previewHtml)
    {
        $this->html = $previewHtml;
    }

    private function reRenderEmailsUsingTemplate(): void
    {
        self::getCampaignClass()::query()
            ->where('status', CampaignStatus::Draft)
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (Campaign $campaign) {
                $campaign->contentItems->where('template_id', $this->template->id)->each(function (ContentItem $contentItem) {
                    $contentItem->setHtml($this->renderHtml($contentItem->getTemplateFieldValues()));
                    $contentItem->save();
                });
            });

        self::getTransactionalMailClass()::query()
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (TransactionalMail $mail) {
                $mail->contentItem->setHtml($this->renderHtml($mail->contentItem->getTemplateFieldValues()));
                $mail->contentItem->save();
            });

        self::getAutomationMailClass()::query()
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (AutomationMail $mail) {
                $mail->contentItem->setHtml($this->renderHtml($mail->contentItem->getTemplateFieldValues()));
                $mail->contentItem->save();
            });
    }

    private function renderHtml(array $fieldValues): string
    {
        $templateRenderer = (new TemplateRenderer($this->template->html ?? ''));

        $html = $templateRenderer->render($fieldValues);
        if (containsMjml($html)) {
            $mjml = Mailcoach::getSharedActionClass('initialize_mjml', InitializeMjmlAction::class)->execute();

            $html = $mjml->toHtml($html);
        }

        return $html;
    }

    public function render(): View
    {
        return view('mailcoach::app.templates.edit')
            ->layout('mailcoach::app.layouts.app', [
                'title' => $this->template->name,
                'originTitle' => __mc('Templates'),
                'originHref' => route('mailcoach.templates'),
            ]);
    }
}
