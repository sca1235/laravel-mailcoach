<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Queries\CampaignUnsubscribesQuery;
use Spatie\Mailcoach\Http\Api\Resources\UnsubscribeResource;

class CampaignUnsubscribesController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $unsubscribes = (new CampaignUnsubscribesQuery($campaign));
        $unsubscribes->with(['contentItem.model', 'subscriber']);

        return UnsubscribeResource::collection($unsubscribes->paginate());
    }
}
