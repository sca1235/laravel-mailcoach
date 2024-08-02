<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;
use Spatie\Navigation\Section;

class BootstrapNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        $navigation = app(MainNavigation::class);

        $navigation->addIf($request->user(config('mailcoach.guard'))?->can('viewMailcoach'), __mc('Overview'), route('mailcoach.dashboard'))
            ->addIf($request->user(config('mailcoach.guard'))?->can('viewAny', self::getCampaignClass()), __mc('Campaigns'), route('mailcoach.campaigns'))
            ->addIf($request->user(config('mailcoach.guard'))?->can('viewAny', self::getAutomationClass()), __mc('Automations'), route('mailcoach.automations'))
            ->addIf($request->user(config('mailcoach.guard'))?->can('viewAny', self::getEmailListClass()), __mc('Lists'), route('mailcoach.emailLists'))
            ->addIf($request->user(config('mailcoach.guard'))?->can('viewAny', self::getTransactionalMailLogItemClass()), __mc('Transactional'), route('mailcoach.transactional'))
            ->addIf($request->user(config('mailcoach.guard'))?->can('viewAny', self::getTemplateClass()), __mc('Templates'), route('mailcoach.templates'));

        foreach (Mailcoach::$mainMenuItems as $item) {
            $navigation->add($item->label, $item->url, function (Section $section) use ($item) {
                if (! $item->children) {
                    return null;
                }

                foreach ($item->children as $child) {
                    $section->add($child->label, $child->url);
                }
            });
        }

        return $next($request);
    }
}
