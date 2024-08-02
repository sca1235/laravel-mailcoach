<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class CreateSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(PendingSubscriber $pendingSubscriber): Subscriber
    {
        $lock = Cache::lock("subscribe-{$pendingSubscriber->email}-{$pendingSubscriber->emailList->id}", seconds: 15);

        try {
            $lock->block(5);

            /** @var class-string<Subscriber> $subscriberClass */
            $subscriberClass = self::getSubscriberClass();

            $subscriber = $subscriberClass::findForEmail($pendingSubscriber->email, $pendingSubscriber->emailList);

            $wasAlreadySubscribed = $subscriber?->isSubscribed();

            if (! $subscriber) {
                $subscriber = new $subscriberClass([
                    'email' => $pendingSubscriber->email,
                    'email_list_id' => $pendingSubscriber->emailList->id,
                ]);
            }

            $subscriber->fill([
                'email' => $pendingSubscriber->email,
                'subscribed_at' => $subscriber->subscribed_at ?? $pendingSubscriber->subscribedAt ?? now(),
                'unsubscribed_at' => $pendingSubscriber->unsubscribedAt,
            ]);

            $subscriber->fill($pendingSubscriber->attributes);

            if (! $wasAlreadySubscribed && $pendingSubscriber->emailList->requires_confirmation && $pendingSubscriber->respectDoubleOptIn) {
                $subscriber->subscribed_at = null;
            }

            $subscriber->extra_attributes = array_merge($subscriber->extra_attributes->toArray(), $pendingSubscriber->extraAttributes);

            $subscriber->save();

            if ($pendingSubscriber->replaceTags) {
                $subscriber->syncTags($pendingSubscriber->tags);
            } elseif ($pendingSubscriber->tags) {
                $subscriber->addTags($pendingSubscriber->tags);
            }

            if ($subscriber->isUnconfirmed()) {
                $sendConfirmSubscriberMailAction = Mailcoach::getAudienceActionClass('send_confirm_subscriber_mail', SendConfirmSubscriberMailAction::class);

                $sendConfirmSubscriberMailAction->execute($subscriber, $pendingSubscriber->redirectAfterSubscribed);
            }

            $subscriber->refresh();

            if ($subscriber->isSubscribed() && ! $subscriber->imported_via_import_uuid) {
                event(new SubscribedEvent($subscriber));
            }

            return $subscriber;
        } finally {
            optional($lock)->release();
        }
    }
}
