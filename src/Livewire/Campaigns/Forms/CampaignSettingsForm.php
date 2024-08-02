<?php

namespace Spatie\Mailcoach\Livewire\Campaigns\Forms;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class CampaignSettingsForm extends Form
{
    use UsesMailcoachModels;

    public bool $dirty = false;

    public Campaign $campaign;

    public string $name;

    public ?string $from_email = null;

    public ?string $from_name = null;

    public ?string $reply_to_email = null;

    public ?string $reply_to_name = null;

    public ?int $email_list_id;

    public ?bool $utm_tags = false;

    public ?string $utm_source = null;

    public ?string $utm_medium = null;

    public ?string $utm_campaign = null;

    public ?bool $add_subscriber_tags = false;

    public ?bool $add_subscriber_link_tags = false;

    public ?int $segment_id = null;

    public ?bool $show_publicly = true;

    public ?bool $enable_webview = true;

    public function setCampaign(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->name = $campaign->name;
        $this->segment_id = $campaign->segment_id;
        $this->show_publicly = $campaign->show_publicly;
        $this->email_list_id = $campaign->email_list_id;
        $this->enable_webview = ! $campaign->disable_webview;

        $this->from_email = $campaign->contentItem->from_email;
        $this->from_name = $campaign->contentItem->from_name;
        $this->reply_to_email = $campaign->contentItem->reply_to_email;
        $this->reply_to_name = $campaign->contentItem->reply_to_name;
        $this->utm_tags = $campaign->contentItem->utm_tags ?? false;
        $this->utm_medium = $campaign->contentItem->utm_medium ?? 'email';
        $this->utm_source = $campaign->contentItem->utm_source ?? 'newsletter';
        $this->utm_campaign = $campaign->contentItem->utm_campaign ?? Str::slug($campaign->name);
        $this->add_subscriber_tags = $campaign->contentItem->add_subscriber_tags ?? false;
        $this->add_subscriber_link_tags = $campaign->contentItem->add_subscriber_link_tags ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'from_email' => ['nullable', 'email:strict'],
            'from_name' => 'nullable',
            'reply_to_email' => ['nullable', new Delimited('email:strict')],
            'reply_to_name' => ['nullable', new Delimited('string')],
            'email_list_id' => Rule::exists(self::getEmailListClass(), 'id'),
            'utm_tags' => 'bool',
            'utm_source' => 'nullable',
            'utm_medium' => 'nullable',
            'utm_campaign' => 'nullable|alpha_dash',
            'add_subscriber_tags' => 'bool',
            'add_subscriber_link_tags' => 'bool',
            'segment_id' => ['required_if:segment,segment'],
            'show_publicly' => ['nullable', 'bool'],
            'enable_webview' => ['nullable', 'bool'],
        ];
    }

    public function save(string $segment): void
    {
        $this->validate();

        $this->campaign->fill([
            'name' => $this->name,
            'segment_id' => $this->segment_id,
            'show_publicly' => $this->show_publicly,
            'email_list_id' => $this->email_list_id,
            'disable_webview' => ! $this->enable_webview,
        ]);

        $this->campaign->contentItems->each(function (ContentItem $contentItem) {
            $contentItem->fill([
                'from_email' => $this->from_email,
                'from_name' => $this->from_name,
                'reply_to_email' => $this->reply_to_email,
                'reply_to_name' => $this->reply_to_name,
                'utm_tags' => $this->utm_tags,
                'utm_source' => $this->utm_tags ? $this->utm_source : null,
                'utm_medium' => $this->utm_tags ? $this->utm_medium : null,
                'utm_campaign' => $this->utm_tags ? Str::slug($this->utm_campaign) : null,
                'add_subscriber_tags' => $this->add_subscriber_tags,
                'add_subscriber_link_tags' => $this->add_subscriber_link_tags,
            ]);

            $contentItem->save();
        });

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->campaign->usingCustomSegment()) {
            $segmentClass = $this->campaign->segment_class;
        }

        $this->campaign->fill([
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $this->campaign->segment_id,
        ]);

        $this->campaign->save();
        $this->campaign->update(['segment_description' => $this->campaign->getSegment()->description()]);

        $this->dirty = false;
    }
}
