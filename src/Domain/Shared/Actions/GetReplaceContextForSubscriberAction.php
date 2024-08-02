<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class GetReplaceContextForSubscriberAction
{
    public function __construct(
    ) {
    }

    public function execute(?Subscriber $subscriber, ?Send $send = null): array
    {
        if (! $subscriber) {
            return [];
        }

        $context = [];

        $context = array_merge($context, $this->getContextForList($subscriber->emailList));
        $context = array_merge($context, $this->getContextForSubscriber($subscriber, $send));

        return $context;
    }

    protected function getContextForList(?EmailList $emailList): array
    {
        if (! $emailList) {
            return [];
        }

        $attributes = [
            'uuid' => $emailList->uuid,
            'name' => $emailList->name,
            'website_url' => $emailList->websiteUrl(),
            'websiteUrl' => $emailList->websiteUrl(),
        ];

        return [
            'list' => $attributes,
            'emailList' => $attributes,
            'email_list' => $attributes,
            'websiteUrl' => $emailList->websiteUrl(),
            'website_url' => $emailList->websiteUrl(),
        ];
    }

    protected function getContextForSubscriber(Subscriber $subscriber, ?Send $send = null): array
    {
        $context = [
            'unsubscribeUrl' => $subscriber->unsubscribeUrl($send),
            'preferencesUrl' => $subscriber->preferencesUrl($send),
            'subscriber' => array_merge(
                $subscriber->extra_attributes->toArray(),
                array_filter([
                    'uuid' => $subscriber->uuid,
                    'first_name' => $subscriber->first_name,
                    'last_name' => $subscriber->last_name,
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->subscribed_at,
                    'extra_attributes' => $subscriber->extra_attributes->toArray(),
                ]),
            ),
        ];

        $tagUrls = $subscriber->tags->mapWithKeys(function (Tag $tag) use ($subscriber, $send) {
            return [$tag->name => $subscriber->unsubscribeTagUrl($tag->name, $send)];
        })->toArray();

        $context['unsubscribeTag'] = $tagUrls;

        return $context;
    }
}
