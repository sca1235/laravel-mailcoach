<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\Http\Api\Resources\LinkResource;

class CampaignClicksController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaignLinks = new CampaignLinksQuery($campaign);

        return LinkResource::collection($campaignLinks->paginate());
    }
}
