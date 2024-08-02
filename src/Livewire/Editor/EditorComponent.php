<?php

namespace Spatie\Mailcoach\Livewire\Editor;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\Template\Support\TemplateRenderer;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mjml\Exceptions\CouldNotConvertMjml;
use Spatie\Mjml\Mjml;

abstract class EditorComponent extends Component
{
    use UsesMailcoachModels;

    public static bool $supportsTemplates = true;

    public static bool $supportsContent = true;

    public HasHtmlContent $model;

    public int|string|null $templateId = null;

    public ?Template $template = null;

    public array $templateFieldValues = [];

    public string $fullHtml = '';

    public string $emails = '';

    public bool $hasError = false;

    private Mjml $mjml;

    public function __construct()
    {
        $this->mjml = Mailcoach::getSharedActionClass('initialize_mjml', InitializeMjmlAction::class)->execute();
    }

    public function mount(HasHtmlContent $model)
    {
        $this->model = $model;

        $this->templateFieldValues = $model->getTemplateFieldValues();

        if ($model->hasTemplates()) {
            $this->template = $model->template;
            $this->templateId = $model->template?->id;
        }

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[explode(':', $placeHolderName)[0]] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= $this->template?->getHtml() ?? '';
        }

        $this->renderFullHtml();

        $this->dispatch('editorUpdated', $this->model->uuid, $this->previewHtml());
    }

    public function updatingTemplateId(int|string|null $templateId)
    {
        if (! $templateId) {
            $this->template = null;

            return;
        }

        $oldTemplate = $this->template;

        $this->template = self::getTemplateClass()::find($templateId);

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } elseif (is_null($this->templateFieldValues['html']) || $this->templateFieldValues['html'] === '' || $this->templateFieldValues['html'] === $oldTemplate->getHtml()) {
            $this->templateFieldValues['html'] = $this->template?->getHtml() ?? '';
        }
    }

    public function updated()
    {
        $this->renderFullHtml();

        $this->dispatch('editorUpdated', $this->model->uuid, $this->previewHtml());
    }

    public function renderFullHtml()
    {
        if ($this->template) {
            $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
            $this->fullHtml = $templateRenderer->render($this->templateFieldValues);

            if (containsMjml($this->fullHtml)) {
                $this->fullHtml = $this->mjml->toHtml($this->fullHtml);
            }

            unset($this->previewHtml);

            return;
        }

        $html = $this->templateFieldValues['html'] ?? '';

        if (is_array($html)) {
            $html = $html['html'] ?? '';
        }

        $this->fullHtml = $html;

        unset($this->previewHtml);
    }

    #[Computed]
    public function previewHtml(): string
    {
        $html = $this->fullHtml;

        if (containsMjml($html)) {
            try {
                $html = $this->mjml->toHtml($html);
            } catch (CouldNotConvertMjml) {
                // Do nothing in preview
            }
        }

        return $html;
    }

    public function rules(): array
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        return collect($fieldValues)->mapWithKeys(function ($value, $key) {
            return ["templateFieldValues.{$key}" => ['required', new HtmlRule()]];
        })->toArray();
    }

    #[On('saveContentQuietly')]
    public function saveQuietly()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        if ($this->model->hasTemplates()) {
            $this->model->template_id = $this->template?->id;
        }

        if (! empty($this->rules)) {
            $this->validate($this->rules());
        }

        $this->hasError = false;

        try {
            Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class)->execute(htmlspecialchars_decode($this->fullHtml));
        } catch (\Throwable $e) {
            notifyError($e->getMessage());
            $this->hasError = true;

            return;
        }

        $this->model->setHtml($this->fullHtml);
        $this->model->setTemplateFieldValues($fieldValues);
        $this->model->save();
        $this->dispatch('editorSavedQuietly');
    }

    #[On('saveContent')]
    public function save()
    {
        foreach ($this->templateFieldValues as $html) {
            $html = is_array($html)
                ? $html['html'] ?? ''
                : $html;

            if (! $this->isAllowedToSave($html)) {
                return;
            }
        }

        $this->saveQuietly();

        if (! $this->hasError) {
            $this->dispatch('editorSaved');
        }
    }

    protected function filterNeededFields(array $fields, ?Template $template): array
    {
        if (! $template) {
            return Arr::only($fields, 'html');
        }

        if (! $template->containsPlaceHolders()) {
            return Arr::only($fields, 'html');
        }

        return Arr::only($fields, Arr::pluck($template->fields(), 'name'));
    }

    protected function isAllowedToSave(string $html): bool
    {
        if (! containsMjml($html)) {
            return true;
        }

        try {
            $result = $this->mjml->convert($html);
        } catch (CouldNotConvertMjml $e) {
            notifyError($e->getMessage());

            return false;
        }

        if ($result->hasErrors()) {
            notifyError(implode("\n", $result->errors()));

            return false;
        }

        return true;
    }

    abstract public function render();
}
