<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Template\Models\Template;

/**
 * @property ?Template $template
 * @property int|string|null $template_id
 * @property ?CarbonInterface $updated_at
 *
 * @mixin ContentItem
 */
interface HasHtmlContent
{
    public function getModel(): ?Model;

    public function hasTemplates(): bool;

    public function getHtml(): ?string;

    public function setHtml(string $html): void;

    public function getStructuredHtml(): ?string;

    public function getTemplateFieldValues(): array;

    public function setTemplateFieldValues(array $fieldValues = []): self;
}
