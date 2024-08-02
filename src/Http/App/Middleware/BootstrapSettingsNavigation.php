<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Navigation\Section;

class BootstrapSettingsNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        $navigation = resolve(SettingsNavigation::class);

        foreach (Mailcoach::$settingsMenuItems['before'] as $item) {
            $this->addItem($navigation, $item);
        }

        $navigation->addIf(Auth::guard(config('mailcoach.guard'))->user()->can('viewMailcoach'), __mc('Configuration'), route('general-settings'), function (Section $section) {
            $section
                ->add(__mc('General'), route('general-settings'))
                ->add(__mc('Mailers'), route('mailers'))
                ->add(__mc('Suppressions'), route('suppressions'))
                ->add(__mc('Editor'), route('editor'))
                ->add(__mc('Webhooks'), route('webhooks'));
        });

        foreach (Mailcoach::$settingsMenuItems['after'] as $item) {
            $this->addItem($navigation, $item);
        }

        return $next($request);
    }

    public function addItem(SettingsNavigation $navigation, mixed $item): void
    {
        $navigation->add($item->label, $item->url, function (Section $section) use ($item) {
            if (! $item->children) {
                return null;
            }

            foreach ($item->children as $child) {
                $section->add($child->label, $child->url);
            }
        });
    }
}
