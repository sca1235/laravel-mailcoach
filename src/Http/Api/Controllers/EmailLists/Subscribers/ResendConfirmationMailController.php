<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Symfony\Component\HttpFoundation\Response;

class ResendConfirmationMailController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(
        Request $request,
        EmailList $emailList,
        Subscriber $subscriber,
        SendConfirmSubscriberMailAction $sendConfirmSubscriberMailAction
    ) {
        if ($emailList->exists && ! $subscriber->exists) {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $subscriber = Subscriber::findForEmail($request->email, $emailList);

            abort_if(is_null($subscriber), Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $subscriber->emailList);

        $this->ensureUnconfirmedSubscribed($subscriber);

        $sendConfirmSubscriberMailAction->execute($subscriber);

        return $this->respondOk();
    }

    protected function ensureUnconfirmedSubscribed(Subscriber $subscriber): void
    {
        if (! $subscriber->isUnconfirmed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email is not unconfirmed');
        }
    }
}
