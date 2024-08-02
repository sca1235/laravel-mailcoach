<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Http\Api\Requests\EmailLists\Subscribers\UpdateSubscriberRequest;
use Spatie\Mailcoach\Http\Api\Requests\StoreSubscriberRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;

class SubscribersController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(EmailList $emailList, Request $request)
    {
        $this->authorize('view', $emailList);

        $subscribers = new EmailListSubscribersQuery($emailList);
        $subscribers->addSelect(
            self::getSubscriberTableName().'.*',
            DB::raw("'{$emailList->uuid}' as email_list_uuid"),
        );

        return SubscriberResource::collection($subscribers->paginate());
    }

    public function show(Subscriber $subscriber)
    {
        $this->authorize('view', $subscriber->emailList);

        return new SubscriberResource($subscriber);
    }

    public function store(StoreSubscriberRequest $request, EmailList $emailList)
    {
        $this->authorize('update', $emailList);
        $this->authorize('create', self::getSubscriberClass());

        /** @var \Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber $pendingSubscriber */
        $pendingSubscriber = $this
            ->getSubscriberClass()::createWithEmail($request->email)
            ->withAttributes($request->subscriberAttributes());

        if ($request->skip_confirmation) {
            $pendingSubscriber->skipConfirmation();
        }

        $subscriber = $pendingSubscriber->subscribeTo($emailList);

        if ($request->has('tags')) {
            $subscriber->syncTags($request->get('tags'));
        }

        return new SubscriberResource($subscriber);
    }

    public function destroy(Subscriber $subscriber)
    {
        $this->authorize('update', $subscriber->emailList);

        app(DeleteSubscriberAction::class)->execute($subscriber);

        return $this->respondOk();
    }

    public function update(Subscriber $subscriber, UpdateSubscriberRequest $request, UpdateSubscriberAction $updateSubscriberAction)
    {
        $this->authorize('update', $subscriber->emailList);

        if ($request->append_tags) {
            $updateSubscriberAction->appendTags();
        }

        $updateSubscriberAction->execute(
            $subscriber,
            $request->subscriberAttributes(),
            $request->tags ?? [],
        );

        return new SubscriberResource($subscriber->refresh());
    }
}
