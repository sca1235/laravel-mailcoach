<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ManagePreferencesController
{
    use UsesMailcoachModels;

    public function show(string $subscriberUuid, ?string $sendUuid = null)
    {
        $updated = cache()->pull('updated-'.$subscriberUuid, false);

        if ($subscriberUuid === 'example') {
            $emailList = self::getEmailListClass()::findByUuid($sendUuid);
            $subscriberClass = self::getSubscriberClass();
            $subscriber = new $subscriberClass([
                'uuid' => Str::uuid(),
                'email_list_id' => $emailList->id,
            ]);
            $tags = $emailList->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->get();

            return view('mailcoach::landingPages.manage-preferences', [
                'subscriber' => $subscriber,
                'tags' => $tags,
            ]);
        }

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = self::getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        $send = $subscriber->sends()->where('uuid', $sendUuid)->first();

        $tags = $emailList->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->get();

        return view('mailcoach::landingPages.manage-preferences', compact('emailList', 'subscriber', 'send', 'tags', 'updated'));
    }

    public function updatePersonalInfo(Request $request, string $subscriberUuid)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = self::getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $subscriber->update([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
        ]);

        cache()->put('updated-'.$subscriberUuid, true);

        return redirect()->back();
    }

    public function updateSubscriptions(Request $request, string $subscriberUuid, ?string $sendUuid = null)
    {

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = self::getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = self::getSendClass()::findByUuid($sendUuid ?? '');

        if ($request->get('unsubscribe_from_all')) {
            $subscriber->unsubscribe($send);

            $emailList = $subscriber->emailList;

            return $emailList->redirect_after_unsubscribed
                ? redirect()->to($emailList->redirect_after_unsubscribed)
                : view('mailcoach::landingPages.unsubscribed', compact('emailList', 'subscriber'));
        }

        $subscriber->syncPreferenceTags(array_keys($request->get('tags', [])));

        cache()->put('updated-'.$subscriberUuid, true);

        return redirect()->back();
    }
}
