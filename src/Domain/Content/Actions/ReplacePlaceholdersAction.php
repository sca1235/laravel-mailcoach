<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\GetReplaceContextForSendableAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Mailcoach;

class ReplacePlaceholdersAction
{
    protected RenderTwigAction $renderTwigAction;

    public function __construct(
        protected GetReplaceContextForSendableAction $getReplaceContextForSendableAction,
    ) {
        $this->renderTwigAction = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class);
    }

    public function execute(?string $text, Sendable $sendable): string
    {
        $text = $this->renderTwigAction->execute(
            $text ?? '',
            $this->getReplaceContextForSendableAction->execute($sendable)
        );

        return match (true) {
            $sendable instanceof Campaign => $sendable->contentItem->getReplacers()
                ->filter(fn ($replacer) => $replacer instanceof CampaignReplacer)
                ->reduce(fn (string $text, CampaignReplacer $replacer) => $replacer->replace($text, $sendable), $text),
            $sendable instanceof AutomationMail => $sendable->contentItem->getReplacers()
                ->filter(fn ($replacer) => $replacer instanceof AutomationMailReplacer)
                ->reduce(fn (string $text, AutomationMailReplacer $replacer) => $replacer->replace($text, $sendable), $text),
            default => $text,
        };
    }
}
