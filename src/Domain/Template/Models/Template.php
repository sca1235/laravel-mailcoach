<?php

namespace Spatie\Mailcoach\Domain\Template\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Database\Factories\TemplateFactory;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Support\TemplateRenderer;

class Template extends Model implements HasHtmlContent
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
        'contains_placeholders' => 'bool',
        'html' => 'string',
    ];

    public function getTemplateFieldValues(): array
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        return $structuredHtml['templateValues'] ?? ['html' => $this->getHtml()];
    }

    public function setTemplateFieldValues(array $fieldValues = []): self
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        $structuredHtml['templateValues'] = $fieldValues;

        $this->structured_html = json_encode($structuredHtml);

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function getModel(): Model
    {
        return $this;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    protected static function newFactory(): TemplateFactory
    {
        return new TemplateFactory();
    }

    public function containsPlaceHolders(): bool
    {
        return (new TemplateRenderer($this->getHtml()))->containsPlaceHolders();
    }

    public function placeHolderNames(): array
    {
        return (new TemplateRenderer($this->getHtml()))->placeHolderNames();
    }

    public function fields(): array
    {
        return (new TemplateRenderer($this->getHtml()))->fields();
    }

    public function hasTemplates(): bool
    {
        return false;
    }
}
