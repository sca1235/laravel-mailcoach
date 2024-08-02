<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Queries\CampaignBouncesQuery;
use Spatie\Mailcoach\Http\Api\Resources\BounceResource;

class CampaignBouncesController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $query = new CampaignBouncesQuery($campaign);

        return BounceResource::collection($query->paginate());
    }
}
