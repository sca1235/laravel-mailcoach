<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DuplicateAutomationAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation): Automation
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Models\Automation $duplicateAutomation */
        $duplicateAutomation = self::getAutomationClass()::create([
            'name' => __mc('Duplicate of').' '.$automation->name,
            'email_list_id' => $automation->email_list_id,
        ]);

        $automation->actions->each(function (Action $action) use ($duplicateAutomation) {
            $actionClass = static::getAutomationActionClass();
            $newAction = $duplicateAutomation->actions()->save($actionClass::make([
                'action' => $action->action->duplicate(),
                'key' => $action->key,
                'order' => $action->order,
            ]));

            foreach ($action->children as $child) {
                $duplicateAutomation->actions()->save($actionClass::make([
                    'parent_id' => $newAction->id,
                    'action' => $child->action->duplicate(),
                    'key' => $child->key,
                    'order' => $child->order,
                ]));
            }
        });

        return $duplicateAutomation;
    }
}
