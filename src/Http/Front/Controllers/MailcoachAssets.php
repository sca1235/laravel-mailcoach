<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Livewire\Drawer\Utils;

class MailcoachAssets
{
    public function script()
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../../../../resources/dist/manifest.json'), true);

        $fileName = $manifest['resources/js/app.js']['file'];

        return Utils::pretendResponseIsFile(__DIR__."/../../../../resources/dist/{$fileName}");
    }

    public function style()
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../../../../resources/dist/manifest.json'), true);

        $fileName = $manifest['resources/css/app.css']['file'];

        return Utils::pretendResponseIsFile(__DIR__."/../../../../resources/dist/{$fileName}", 'text/css');
    }
}
