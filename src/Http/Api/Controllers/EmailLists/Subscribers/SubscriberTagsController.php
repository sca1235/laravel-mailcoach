<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;

class SubscriberTagsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function update(Request $request, Subscriber $subscriber)
    {
        $this->authorize('view', $subscriber);

        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string',
        ]);

        $subscriber->addTags($request->get('tags'));

        return new SubscriberResource($subscriber);
    }

    public function destroy(Request $request, Subscriber $subscriber)
    {
        $this->authorize('view', $subscriber);

        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string',
        ]);

        $subscriber->removeTags($request->get('tags'));

        return new SubscriberResource($subscriber);
    }
}
