<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mjml\Mjml;

class InitializeMjmlAction
{
    public function execute(): Mjml
    {
        $mjml = Mjml::new();

        /**
         * When Sidecar Mjml is configured and set up, we want to highlight through
         * that function instead of calling Mjml through node directly.
         */
        if (in_array(\Spatie\MjmlSidecar\MjmlFunction::class, config('sidecar.functions', []))) {
            return $mjml->sidecar();
        }

        return $mjml;
    }
}
