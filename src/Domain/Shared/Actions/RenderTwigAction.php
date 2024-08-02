<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class RenderTwigAction
{
    public function execute(string $html, array $context = []): string
    {
        $twig = new Environment(new ArrayLoader(), [
            'autoescape' => 'html',
        ]);

        $html = rawurldecode($html);

        /** This is in case an editor urlencodes {{ variable }} to {{+variable+}} */
        $html = str_replace(['{{+', '+}}'], ['{{ ', ' }}'], $html);

        return $twig->createTemplate($html)->render($context);
    }
}
