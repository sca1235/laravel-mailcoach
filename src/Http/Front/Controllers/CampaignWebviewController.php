<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignWebviewController
{
    use UsesMailcoachModels;

    public function __invoke(string $campaignUuid)
    {
        /** @var Campaign $campaign */
        $campaign = static::getCampaignClass()::findByUuid($campaignUuid);

        if (! $campaign) {
            abort(404);
        }

        if ($campaign->isDraft()) {
            abort(404);
        }

        return view('mailcoach::campaign.webview', compact('campaign'));
    }
}
