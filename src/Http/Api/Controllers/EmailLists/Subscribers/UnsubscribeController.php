<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(
        Request $request,
        EmailList $emailList,
        Subscriber $subscriber
    ) {
        if ($emailList->exists && ! $subscriber->exists) {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $subscriber = Subscriber::findForEmail($request->email, $emailList);

            abort_if(is_null($subscriber), Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $subscriber->emailList);

        $this->ensureSubscribedSubscriber($subscriber);

        $subscriber->unsubscribe();

        return $this->respondOk();
    }

    protected function ensureSubscribedSubscriber(Subscriber $subscriber): void
    {
        if (! $subscriber->isSubscribed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email was not subscribed');
        }
    }
}
