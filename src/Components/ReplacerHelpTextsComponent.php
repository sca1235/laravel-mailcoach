<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\ReplacerWithHelpText as AutomationReplacerWithHelpTextAlias;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\ReplacerWithHelpText as CampaignReplacerWithHelpText;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class ReplacerHelpTextsComponent extends Component
{
    use UsesMailcoachModels;

    public function __construct(private Model $model)
    {
    }

    public function replacerHelpTexts(): array
    {
        return match (true) {
            $this->model instanceof ContentItem => $this->model->getReplacers()->flatMap(fn ($replacer) => $replacer->helpText())->toArray(),
            $this->model instanceof Campaign => $this->campaignReplacerHelpTexts(),
            $this->model instanceof AutomationMail => $this->automationReplacerHelpTexts(),
            $this->model instanceof TransactionalMail => $this->transactionalMailTemplateReplacerHelpTexts(),
            $this->model instanceof Template => $this->templateReplacerHelpTexts(),
            default => [],
        };
    }

    public function templateReplacerHelpTexts(): array
    {
        return array_intersect_key($this->automationReplacerHelpTexts(), $this->campaignReplacerHelpTexts());
    }

    public function automationReplacerHelpTexts(): array
    {
        return (new (self::getContentItemClass()))
            ->getReplacers()
            ->flatMap(fn (AutomationReplacerWithHelpTextAlias $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function campaignReplacerHelpTexts(): array
    {
        return (new (self::getContentItemClass()))
            ->getReplacers()
            ->flatMap(fn (CampaignReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function transactionalMailTemplateReplacerHelpTexts(): array
    {
        /** @var TransactionalMail $model */
        $model = $this->model;

        return collect($model->replacers)
            ->map(fn (string $replacerKeyInConfig) => config("mailcoach.transactional.replacers.{$replacerKeyInConfig}"))
            ->filter()
            ->filter(fn (string $className) => class_exists($className))
            ->map(fn (string $className) => resolve($className))
            ->flatMap(fn (TransactionalMailReplacer $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
