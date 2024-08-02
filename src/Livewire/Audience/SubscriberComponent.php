<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;

class SubscriberComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public Subscriber $subscriber;

    public string $email;

    public ?string $first_name = '';

    public ?string $last_name = '';

    public array $tags = [];

    public array $extra_attributes = [];

    public EmailList $emailList;

    public int $totalSendsCount;

    #[Url]
    public string $tab = 'profile';

    protected $listeners = [
        'tags-updated' => 'updateTags',
    ];

    protected function rules(): array
    {
        return [
            'email' => [
                'email:strict',
                Rule::unique(self::getSubscriberTableName(), 'email')
                    ->where('email_list_id', $this->emailList->id)
                    ->ignore($this->subscriber->id),
            ],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'tags' => 'array',
            'extra_attributes' => ['nullable', 'array'],
        ];
    }

    public function updateTags(array|string ...$tags): void
    {
        $this->tags = Arr::wrap($tags);
    }

    public function save(): void
    {
        $this->validate();

        /** @var UpdateSubscriberAction $updateSubscriberAction */
        $updateSubscriberAction = Mailcoach::getAudienceActionClass('update_subscriber', UpdateSubscriberAction::class);
        $updateSubscriberAction->execute(
            subscriber: $this->subscriber,
            attributes: [
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ],
            tags: $this->tags ?? [],
        );

        $this->saveAttributes();
    }

    public function saveAttributes(): void
    {
        $this->subscriber->extra_attributes = null;

        foreach ($this->extra_attributes as $extraAttribute) {
            $this->subscriber->extra_attributes[$extraAttribute['key']] = $extraAttribute['value'];
        }

        $this->subscriber->save();

        notify(__mc('Subscriber :subscriber was updated.', ['subscriber' => $this->subscriber->email]));
    }

    public function mount(EmailList $emailList, Subscriber $subscriber): void
    {
        $this->authorize('update', $subscriber);

        $this->emailList = $emailList;
        $this->subscriber = $subscriber;
        $this->fill($this->subscriber->toArray());

        $this->totalSendsCount = $this->subscriber->sends()->count();
        $this->tags = $this->subscriber->tags()->where('type', TagType::Default)->pluck('name')->toArray();
        $this->extra_attributes = $this->subscriber->extra_attributes->map(function ($value, $key) {
            return [
                'key' => $key,
                'value' => $value,
            ];
        })->where('key', '!=', '')->values()->toArray();

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Subscribers'), route('mailcoach.emailLists.subscribers', $this->emailList));
            });
    }

    public function addAttribute(): void
    {
        $this->extra_attributes[] = [];
    }

    public function unsubscribe(): void
    {
        if (! $this->subscriber->isSubscribed()) {
            notify(__mc('Can only unsubscribe a subscribed subscriber'), 'error');

            return;
        }

        $this->subscriber->unsubscribe();

        notify(__mc(':subscriber has been unsubscribed.', ['subscriber' => $this->subscriber->email]));
    }

    public function confirm(): void
    {
        if ($this->subscriber->status !== SubscriptionStatus::Unconfirmed) {
            notify(__mc('Can only subscribe unconfirmed emails'), 'error');

            return;
        }

        $this->subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        notify(__mc(':subscriber has been confirmed.', ['subscriber' => $this->subscriber->email]));
    }

    public function resubscribe(): void
    {
        if (! $this->subscriber->isUnsubscribed()) {
            notify(__mc('Can only resubscribe unsubscribed subscribers'), 'error');

            return;
        }

        $this->subscriber->resubscribe();

        notify(__mc(':subscriber has been resubscribed.', ['subscriber' => $this->subscriber->email]));
    }

    public function resendConfirmation(): void
    {
        resolve(SendConfirmSubscriberMailAction::class)->execute($this->subscriber);

        notify(__mc('A confirmation mail has been sent to :subscriber', ['subscriber' => $this->subscriber->email]));
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->subscriber);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($this->subscriber);

        notify(__mc('Subscriber :subscriber was deleted.', ['subscriber' => $this->subscriber->email]));

        $this->redirectRoute('mailcoach.emailLists.subscribers', $this->emailList);
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.subscribers.show')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'emailList' => $this->emailList,
                'title' => $this->subscriber->email,
                'originTitle' => $this->emailList->name,
                'originHref' => route('mailcoach.emailLists.subscribers', $this->emailList),
            ]);
    }
}
